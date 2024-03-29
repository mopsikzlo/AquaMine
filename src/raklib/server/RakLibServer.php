<?php

/*
 * RakLib network library
 *
 *
 * This project is not affiliated with Jenkins Software LLC nor RakNet.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

declare(strict_types=1);

namespace raklib\server;

use pocketmine\snooze\SleeperNotifier;
use raklib\generic\Socket;
use raklib\RakLib;
use raklib\utils\InternetAddress;
use function array_reverse;
use function error_get_last;
use function error_reporting;
use function function_exists;
use function gc_enable;
use function get_class;
use function getcwd;
use function gettype;
use function ini_set;
use function is_object;
use function method_exists;
use function mt_rand;
use function realpath;
use function register_shutdown_function;
use function str_replace;
use function strval;
use function substr;
use function xdebug_get_function_stack;
use const DIRECTORY_SEPARATOR;
use const PHP_INT_MAX;
use const PTHREADS_INHERIT_NONE;

class RakLibServer extends \Thread{
	/** @var InternetAddress */
	private $address;

	/** @var \ThreadedLogger */
	protected $logger;

	/** @var \ClassLoader */
	protected $classLoader;

	/** @var bool */
	protected $shutdown = false;
	/** @var bool */
	protected $ready = false;

	/** @var \Threaded */
	protected $externalQueue;
	/** @var \Threaded */
	protected $internalQueue;

	/** @var string */
	protected $mainPath;

	/** @var int */
	protected $serverId = 0;
	/** @var int */
	protected $maxMtuSize;
	/** @var \Volatile|int[] */
	private $protocolVersions;

	/** @var SleeperNotifier|null */
	protected $mainThreadNotifier;
	/** @var SleeperNotifier|null */
	protected $internalThreadNotifier;

	/** @var \Throwable|null */
	public $crashInfo = null;

	/**
	 * @param \ThreadedLogger      $logger
	 * @param \ClassLoader         $classLoader
	 * @param InternetAddress      $address
	 * @param int                  $maxMtuSize
	 * @param int[]                $protocolVersions
	 * @param SleeperNotifier|null $sleeper
	 */
	public function __construct(\ThreadedLogger $logger, \ClassLoader $classLoader, InternetAddress $address, int $maxMtuSize = 1492, array $protocolVersions = [], ?SleeperNotifier $sleeper = null){
		$this->address = $address;

		$this->serverId = mt_rand(0, PHP_INT_MAX);
		$this->maxMtuSize = $maxMtuSize;

		$this->logger = $logger;
		$this->classLoader = $classLoader;

		$this->externalQueue = new \Threaded;
		$this->internalQueue = new \Threaded;

		if(\Phar::running(true) !== ""){
			$this->mainPath = \Phar::running(true);
		}else{
			$this->mainPath = realpath(getcwd()) . DIRECTORY_SEPARATOR;
		}

		$this->protocolVersions = count($protocolVersions) === 0 ? [RakLib::DEFAULT_PROTOCOL_VERSION] : $protocolVersions;

		$this->mainThreadNotifier = $sleeper;
	}

	public function isShutdown() : bool{
		return $this->shutdown === true;
	}

	public function shutdown() : void{
		$this->shutdown = true;
	}

	/**
	 * Returns the RakNet server ID
	 * @return int
	 */
	public function getServerId() : int{
		return $this->serverId;
	}

	public function getProtocolVersions() : \Volatile{
		return $this->protocolVersions;
	}

	/**
	 * @return \ThreadedLogger
	 */
	public function getLogger() : \ThreadedLogger{
		return $this->logger;
	}

	/**
	 * @return \Threaded
	 */
	public function getExternalQueue() : \Threaded{
		return $this->externalQueue;
	}

	/**
	 * @return \Threaded
	 */
	public function getInternalQueue() : \Threaded{
		return $this->internalQueue;
	}

	public function pushMainToThreadPacket(string $str) : void{
		$this->internalQueue[] = $str;
		if($this->internalThreadNotifier !== null){
			$this->internalThreadNotifier->wakeupSleeper();
		}
	}

	public function readMainToThreadPacket() : ?string{
		return $this->internalQueue->shift();
	}

	public function pushThreadToMainPacket(string $str) : void{
		$this->externalQueue[] = $str;
		if($this->mainThreadNotifier !== null){
			$this->mainThreadNotifier->wakeupSleeper();
		}
	}

	public function readThreadToMainPacket() : ?string{
		return $this->externalQueue->shift();
	}

	public function shutdownHandler(){
		if($this->shutdown !== true){
			$error = error_get_last();
			if($error !== null){ //fatal error
				$this->setCrashInfo(new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
			}

		}
	}

	public function getCrashInfo() : ?\Throwable{
		return $this->crashInfo;
	}

	private function setCrashInfo(\Throwable $e){
		$this->synchronized(function($e){
			$this->crashInfo = $e;
			$this->notify();
		}, $e);
	}

	public function getTrace($start = 0, $trace = null){
		if($trace === null){
			if(function_exists("xdebug_get_function_stack")){
				$trace = array_reverse(xdebug_get_function_stack());
			}else{
				$e = new \Exception();
				$trace = $e->getTrace();
			}
		}

		$messages = [];
		$j = 0;
		for($i = (int) $start; isset($trace[$i]); ++$i, ++$j){
			$params = "";
			if(isset($trace[$i]["args"]) or isset($trace[$i]["params"])){
				if(isset($trace[$i]["args"])){
					$args = $trace[$i]["args"];
				}else{
					$args = $trace[$i]["params"];
				}
				foreach($args as $name => $value){
					$params .= (is_object($value) ? get_class($value) . " " . (method_exists($value, "__toString") ? $value->__toString() : "object") : gettype($value) . " " . @strval($value)) . ", ";
				}
			}
			$messages[] = "#$j " . (isset($trace[$i]["file"]) ? $this->cleanPath($trace[$i]["file"]) : "") . "(" . (isset($trace[$i]["line"]) ? $trace[$i]["line"] : "") . "): " . (isset($trace[$i]["class"]) ? $trace[$i]["class"] . (($trace[$i]["type"] === "dynamic" or $trace[$i]["type"] === "->") ? "->" : "::") : "") . $trace[$i]["function"] . "(" . substr($params, 0, -2) . ")";
		}

		return $messages;
	}

	public function cleanPath($path){
		return str_replace(["\\", ".php", "phar://", str_replace(["\\", "phar://"], ["/", ""], $this->mainPath)], ["/", "", "", ""], $path);
	}

	public function startAndWait(int $options = PTHREADS_INHERIT_NONE) : void{
		$this->start($options);
		$this->synchronized(function(){
			while(!$this->ready and $this->crashInfo === null){
				$this->wait();
			}
			if($this->crashInfo !== null){
				throw $this->crashInfo;
			}
		});
	}

	public function run() : void{
		try{
			if($this->classLoader !== null){
				$this->classLoader->register(true);
			}

			gc_enable();
			ini_set("memory_limit", '-1');

			error_reporting(-1);
			ini_set("display_errors", '1');
			ini_set("display_startup_errors", '1');

			\ErrorUtils::setErrorExceptionHandler();
			register_shutdown_function([$this, "shutdownHandler"]);

			$socket = new Socket($this->address);

			$internalThreadNotifier = new SleeperNotifier();
			$manager = new SessionManager($this, $socket, $this->maxMtuSize, $internalThreadNotifier);
			$this->internalThreadNotifier = $internalThreadNotifier;
			$this->synchronized(function(){
				$this->ready = true;
				$this->notify();
			});
			$manager->run();
		}catch(\Throwable $e){
			$this->setCrashInfo($e);
			$this->logger->logException($e);
		}
	}

}
