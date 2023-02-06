<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

use pocketmine\event\Timings;
use pocketmine\Server;
use pocketmine\utils\Utils;

use function count;
use function mt_rand;

use const PTHREADS_INHERIT_INI;
use const PTHREADS_INHERIT_CONSTANTS;

class AsyncPool{

	private const WORKER_START_OPTIONS = PTHREADS_INHERIT_INI | PTHREADS_INHERIT_CONSTANTS;

	/** @var Server */
	private $server;

	protected $size;

	/** @var int */
	private $workerMemoryLimit;

	/** @var AsyncTask[] */
	private $tasks = [];
	/** @var int[] */
	private $taskWorkers = [];

	/** @var AsyncWorker[] */
	private $workers = [];
	/** @var int[] */
	private $workerUsage = [];
	/** @var int[] */
	private $workerLastUsed = [];

	/** @var \Closure[] */
	private $workerStartHooks = [];

	public function __construct(Server $server, int $size, int $workerMemoryLimit){
		$this->server = $server;
		$this->size = $size;
		$this->workerMemoryLimit = $workerMemoryLimit;
	}

	public function getSize() : int{
		return $this->size;
	}

	public function increaseSize(int $newSize){
		if($newSize > $this->size){
			$this->size = $newSize;
		}
	}

	/**
	 * Registers a Closure callback to be fired whenever a new worker is started by the pool.
	 * The signature should be `function(int $worker) : void`
	 *
	 * This function will call the hook for every already-running worker.
	 *
	 * @param \Closure $hook
	 */
	public function addWorkerStartHook(\Closure $hook) : void{
		Utils::validateCallableSignature(function(int $worker) : void{}, $hook);
		$this->workerStartHooks[spl_object_id($hook)] = $hook;
		foreach($this->workers as $i => $worker){
			$hook($i);
		}
	}

	/**
	 * Removes a previously-registered callback listening for workers being started.
	 *
	 * @param \Closure $hook
	 */
	public function removeWorkerStartHook(\Closure $hook) : void{
		unset($this->workerStartHooks[spl_object_id($hook)]);
	}

	/**
	 * Returns an array of IDs of currently running workers.
	 *
	 * @return int[]
	 */
	public function getRunningWorkers() : array{
		return array_keys($this->workers);
	}

	/**
	 * Fetches the worker with the specified ID, starting it if it does not exist, and firing any registered worker
	 * start hooks.
	 *
	 * @param int $worker
	 *
	 * @return AsyncWorker
	 */
	private function getWorker(int $worker) : AsyncWorker{
		if(!isset($this->workers[$worker])){
			$this->workerUsage[$worker] = 0;
			$this->workers[$worker] = new AsyncWorker($this->server->getLogger(), $worker, $this->workerMemoryLimit);
			$this->workers[$worker]->setClassLoader($this->server->getLoader());
			$this->workers[$worker]->start(self::WORKER_START_OPTIONS);
			foreach($this->workerStartHooks as $hook){
				$hook($worker);
			}
		}
		return $this->workers[$worker];
	}

	public function submitTaskToWorker(AsyncTask $task, int $worker){
		if(isset($this->tasks[$task->getTaskId()]) or $task->isGarbage()){
			return;
		}

		if($worker < 0 or $worker >= $this->size){
			throw new \InvalidArgumentException("Invalid worker $worker");
		}

		$task->progressUpdates = new \Threaded;
		$this->tasks[$task->getTaskId()] = $task;

		$this->getWorker($worker)->stack($task);
		$this->workerUsage[$worker]++;
		$this->taskWorkers[$task->getTaskId()] = $worker;
		$this->workerLastUsed[$worker] = time();
	}

	public function selectWorker() : int{
		$worker = null;
		$minUsage = PHP_INT_MAX;
		foreach($this->workerUsage as $i => $usage){
			if($usage < $minUsage){
				$worker = $i;
				$minUsage = $usage;
				if($usage === 0){
					break;
				}
			}
		}
		if($worker === null or ($minUsage > 0 and count($this->workers) < $this->size)){
			//select a worker to start on the fly
			for($i = 0; $i < $this->size; ++$i){
				if(!isset($this->workers[$i])){
					$worker = $i;
					break;
				}
			}
		}

		assert($worker !== null);
		return $worker;
	}

	public function submitTask(AsyncTask $task){
		if(isset($this->tasks[$task->getTaskId()]) or $task->isGarbage()){
			return;
		}

		$worker = $this->selectWorker();
		$this->submitTaskToWorker($task, $worker);
		return $worker;
	}

	private function removeTask(AsyncTask $task, bool $force = false){
		if(isset($this->taskWorkers[$task->getTaskId()])){
			if(!$force and ($task->isRunning() or !$task->isGarbage())){
				return;
			}
			$this->workerUsage[$this->taskWorkers[$task->getTaskId()]]--;
		}

		unset($this->tasks[$task->getTaskId()]);
		unset($this->taskWorkers[$task->getTaskId()]);

		$task->cleanObject();
	}

	public function removeTasks(){
		do{
			foreach($this->tasks as $task){
				$task->cancelRun();
				$this->removeTask($task);
			}

			if(count($this->tasks) > 0){
				Server::microSleep(25000);
			}
		}while(count($this->tasks) > 0);

		for($i = 0; $i < $this->size; ++$i){
			$this->workerUsage[$i] = 0;
		}

		$this->taskWorkers = [];
		$this->tasks = [];

		$this->collectWorkers();
	}

	private function collectWorkers(){
		foreach($this->workers as $worker){
			$worker->collect();
		}
	}

	public function shutdownUnusedWorkers() : int{
		$time = time();

		$ret = 0;
		foreach($this->workerUsage as $i => $usage){
			if($usage === 0 and (!isset($this->workerLastUsed[$i]) or $this->workerLastUsed[$i] + 300 < $time)){
				$this->workers[$i]->quit();
				unset($this->workers[$i], $this->workerUsage[$i], $this->workerLastUsed[$i]);
				$ret++;
			}
		}
		return $ret;
	}

	public function collectTasks(){
		Timings::$schedulerAsyncTimer->startTiming();

		foreach($this->tasks as $task){
			if(!$task->isGarbage()){
				$task->checkProgressUpdates($this->server);
			}
			if($task->isGarbage() and !$task->isRunning() and !$task->isCrashed()){
				if(!$task->hasCancelledRun()){
					$task->onCompletion($this->server);
					$this->server->getScheduler()->removeLocalComplex($task);
				}

				$this->removeTask($task);
			}elseif($task->isCrashed()){
				$this->server->getLogger()->critical("Could not execute asynchronous task " . (new \ReflectionClass($task))->getShortName() . ": Task crashed");
				$this->removeTask($task, true);
			}
		}

		$this->collectWorkers();

		Timings::$schedulerAsyncTimer->stopTiming();
	}

	public function shutdown() : void{
		$this->collectTasks();
		$this->removeTasks();
		foreach($this->workers as $worker){
			$worker->quit();
		}
		$this->workers = [];
		$this->workerLastUsed = [];
	}
}
