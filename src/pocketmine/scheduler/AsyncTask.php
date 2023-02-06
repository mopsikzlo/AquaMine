<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

use pocketmine\Collectable;
use pocketmine\Server;

/**
 * Class used to run async tasks in other threads.
 *
 * An AsyncTask does not have its own thread. It is queued into an AsyncPool and executed if there is an async worker
 * with no AsyncTask running. Therefore, an AsyncTask SHOULD NOT execute for more than a few seconds. For tasks that
 * run for a long time or infinitely, start another {@link \pocketmine\Thread} instead.
 *
 * WARNING: Do not call PocketMine-MP API methods, or save objects (and arrays containing objects) from/on other Threads!!
 */
use function assert;
use function igbinary_serialize;
use function igbinary_unserialize;

abstract class AsyncTask extends Collectable{

	/** @var AsyncWorker $worker */
	public $worker = null;

	/** @var \Threaded */
	public $progressUpdates;

	private $result = null;
	private $serialized = false;
	private $cancelRun = false;
	/** @var int|null */
	private $taskId = null;

	private $crashed = false;

	/**
	 * Constructs a new instance of AsyncTask. Subclasses don't need to call this constructor unless an argument is to be passed. ONLY construct this class from the main thread.
	 * <br>
	 * If an argument is passed into this constructor, it will be stored in a thread-local storage (in ServerScheduler), which MUST be retrieved through {@link #fetchLocal} when {@link #onCompletion} is called.
	 * Otherwise, a NOTICE level message will be raised and the reference will be removed after onCompletion exits.
	 * <br>
	 * If null or no argument is passed, do <em>not</em> call {@link #fetchLocal}, or an exception will be thrown.
	 * <br>
	 * WARNING: Use this method carefully. It might take a long time before an AsyncTask is completed. PocketMine will keep a strong reference to objects passed in this method.
	 * This may result in a light memory leak. Usually this does not cause memory failure, but be aware that the object may be no longer usable when the AsyncTask completes.
	 * (E.g. a {@link \pocketmine\Level} object is no longer usable because it is unloaded while the AsyncTask is executing, or even a plugin might be unloaded)
	 * Since PocketMine keeps a strong reference, the objects are still valid, but the implementation is responsible for checking whether these objects are still usable.
	 *
	 * @param mixed $complexData the data to store, pass null to store nothing. Scalar types can be safely stored in class properties directly instead of using this thread-local storage.
	 */
	public function __construct($complexData = null){
		if($complexData === null){
			return;
		}

		Server::getInstance()->getScheduler()->storeLocalComplex($this, $complexData);
	}

	public function run(){
		$this->result = null;

		if($this->cancelRun !== true){
			try{
				$this->onRun();
			}catch(\Throwable $e){
				$this->crashed = true;
				$this->worker->handleException($e);
			}
		}

		$this->setGarbage();
	}

	public function isCrashed() : bool{
		return $this->crashed or $this->isTerminated();
	}

	/**
	 * @return mixed
	 */
	public function getResult(){
		return $this->serialized ? igbinary_unserialize($this->result) : $this->result;
	}

	public function cancelRun(){
		$this->cancelRun = true;
	}

	public function hasCancelledRun() : bool{
		return $this->cancelRun === true;
	}

	/**
	 * @return bool
	 */
	public function hasResult() : bool{
		return $this->result !== null;
	}

	/**
	 * @param mixed $result
	 * @param bool  $serialize
	 */
	public function setResult($result, bool $serialize = true){
		$this->result = $serialize ? igbinary_serialize($result) : $result;
		$this->serialized = $serialize;
	}

	public function setTaskId(int $taskId){
		$this->taskId = $taskId;
	}

	/**
	 * @return int|null
	 */
	public function getTaskId(){
		return $this->taskId;
	}

	/**
	 * @see AsyncWorker::getFromThreadStore()
	 *
	 * @param string $identifier
	 * @return mixed
	 */
	public function getFromThreadStore(string $identifier){
		if($this->worker === null or $this->isGarbage()){
			throw new \BadMethodCallException("Objects stored in AsyncWorker thread-local storage can only be retrieved during task execution");
		}
		return $this->worker->getFromThreadStore($identifier);
	}

	/**
	 * @see AsyncWorker::saveToThreadStore()
	 *
	 * @param string $identifier
	 * @param mixed  $value
	 */
	public function saveToThreadStore(string $identifier, $value){
		if($this->worker === null or $this->isGarbage()){
			throw new \BadMethodCallException("Objects can only be added to AsyncWorker thread-local storage during task execution");
		}
		$this->worker->saveToThreadStore($identifier, $value);
	}

	/**
	 * Actions to execute when run
	 *
	 * @return void
	 */
	abstract public function onRun();

	/**
	 * Actions to execute when completed (on main thread)
	 * Implement this if you want to handle the data in your AsyncTask after it has been processed
	 *
	 * @param Server $server
	 *
	 * @return void
	 */
	public function onCompletion(Server $server){

	}

	/**
	 * Call this method from {@link AsyncTask#onRun} (AsyncTask execution thread) to schedule a call to
	 * {@link AsyncTask#onProgressUpdate} from the main thread with the given progress parameter.
	 *
	 * @param mixed $progress A value that can be safely igbinary_serialize()'ed.
	 */
	public function publishProgress($progress){
		$this->progressUpdates[] = igbinary_serialize($progress);
	}

	/**
	 * @internal Only call from AsyncPool.php on the main thread
	 *
	 * @param Server $server
	 */
	public function checkProgressUpdates(Server $server){
		while($this->progressUpdates->count() !== 0){
			$progress = $this->progressUpdates->shift();
			$this->onProgressUpdate($server, igbinary_unserialize($progress));
		}
	}

	/**
	 * Called from the main thread after {@link AsyncTask#publishProgress} is called.
	 * All {@link AsyncTask#publishProgress} calls should result in {@link AsyncTask#onProgressUpdate} calls before
	 * {@link AsyncTask#onCompletion} is called.
	 *
	 * @param Server $server
	 * @param mixed  $progress The parameter passed to {@link AsyncTask#publishProgress}. It is igbinary_serialize()'ed
	 *                         and then igbinary_unserialize()'ed, as if it has been cloned.
	 */
	public function onProgressUpdate(Server $server, $progress){

	}

	/**
	 * Saves mixed data in thread-local storage on the parent thread. You may use this to retain references to objects
	 * or arrays which you need to access in {@link AsyncTask::onCompletion} which cannot be stored as a property of
	 * your task (due to them becoming serialized).
	 *
	 * Scalar types can be stored directly in class properties instead of using this storage.
	 *
	 * WARNING: THIS METHOD SHOULD ONLY BE CALLED FROM THE MAIN THREAD!
	 *
	 * @param mixed $complexData the data to store
	 *
	 * @return void
	 * @throws \BadMethodCallException if called from any thread except the main thread
	 */
	protected function storeLocal($complexData, Server $server = null){
		if($server === null){
			$server = Server::getInstance();
			assert($server !== null, "Call this method only from the main thread!");
		}
		Server::getInstance()->getScheduler()->storeLocalComplex($this, $complexData);
	}

	/**
	 * Call this method from {@link AsyncTask#onCompletion} to fetch the data stored in the constructor, if any, and
	 * clears it from the storage.
	 *
	 * Do not call this method from {@link AsyncTask#onProgressUpdate}, because this method deletes the data and cannot
	 * be used in the next {@link AsyncTask#onProgressUpdate} call or from {@link AsyncTask#onCompletion}. Use
	 * {@link AsyncTask#peekLocal} instead.
	 *
	 * @param Server $server default null
	 *
	 * @return mixed
	 *
	 * @throws \RuntimeException if no data were stored by this AsyncTask instance.
	 */
	protected function fetchLocal(Server $server = null){
		if($server === null){
			$server = Server::getInstance();
			assert($server !== null, "Call this method only from the main thread!");
		}

		return $server->getScheduler()->fetchLocalComplex($this);
	}

	/**
	 * Call this method from {@link AsyncTask#onProgressUpdate} to fetch the data stored in the constructor.
	 *
	 * Use {@link AsyncTask#peekLocal} instead from {@link AsyncTask#onCompletion}, because this method does not delete
	 * the data, and not clearing the data will result in a warning for memory leak after {@link AsyncTask#onCompletion}
	 * finished executing.
	 *
	 * @param Server|null $server default null
	 *
	 * @return mixed
	 *
	 * @throws \RuntimeException if no data were stored by this AsyncTask instance
	 */
	protected function peekLocal(Server $server = null){
		if($server === null){
			$server = Server::getInstance();
			assert($server !== null, "Call this method only from the main thread!");
		}

		return $server->getScheduler()->peekLocalComplex($this);
	}

	public function cleanObject(){
		foreach($this as $p => $v){
			if(!($v instanceof \Threaded)){
				$this->{$p} = null;
			}
		}

		$this->setGarbage();
	}
}

