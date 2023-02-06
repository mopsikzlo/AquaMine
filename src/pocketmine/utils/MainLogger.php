<?php

declare(strict_types=1);

namespace pocketmine\utils;

use LogLevel;
use pocketmine\Server;
use pocketmine\Thread;
use pocketmine\Worker;

use function date;
use function fclose;
use function fopen;
use function fwrite;
use function get_class;
use function is_resource;
use function preg_replace;
use function time;
use function touch;
use function trim;

use const E_COMPILE_ERROR;
use const E_COMPILE_WARNING;
use const E_CORE_ERROR;
use const E_CORE_WARNING;
use const E_DEPRECATED;
use const E_ERROR;
use const E_NOTICE;
use const E_PARSE;
use const E_RECOVERABLE_ERROR;
use const E_STRICT;
use const E_USER_DEPRECATED;
use const E_USER_ERROR;
use const E_USER_NOTICE;
use const E_USER_WARNING;
use const E_WARNING;
use const PHP_EOL;
use const PTHREADS_INHERIT_NONE;

class MainLogger extends \AttachableThreadedLogger{

	/** @var string */
	protected $logFile;
	/** @var bool */
	protected $logToFile = true;
	/** @var \Threaded */
	protected $logStream;
	/** @var bool */
	protected $shutdown;
	/** @var bool */
	protected $logDebug;
	/** @var MainLogger */
	public static $logger = null;

	/** @var bool */
	private $mainThreadHasFormattingCodes = false;

	/** @var string */
	private $timezone;

	public $port = 0;

	/**
	 * @param string $logFile
	 * @param bool $logDebug
	 *
	 * @throws \RuntimeException
	 */
	public function __construct(string $logFile, bool $logDebug = false){
		if(static::$logger instanceof MainLogger){
			throw new \RuntimeException("MainLogger has been already created");
		}
		touch($logFile);
		$this->logFile = $logFile;
		$this->logDebug = $logDebug;
		$this->logStream = new \Threaded;

		//Child threads may not inherit command line arguments, so if there's an override it needs to be recorded here
		$this->mainThreadHasFormattingCodes = Terminal::hasFormattingCodes();
		$this->timezone = ini_get('date.timezone');

		$this->start(PTHREADS_INHERIT_NONE);
	}

	/**
	 * @return MainLogger
	 */
	public static function getLogger() : MainLogger{
		return static::$logger;
	}

	/**
	 * Assigns the MainLogger instance to the {@link MainLogger#logger} static property.
	 *
	 * WARNING: Because static properties are thread-local, this MUST be called from the body of every Thread if you
	 * want the logger to be accessible via {@link MainLogger#getLogger}.
	 */
	public function registerStatic(){
		if(static::$logger === null){
			static::$logger = $this;
		}
	}
	
	public function setLogToFile(bool $logToFile){
		$this->logToFile = $logToFile;
		if(!$logToFile){
			@unlink($this->logFile);
		}
	}

	public function emergency($message){
		$this->send($message, \LogLevel::EMERGENCY, "EMERGENCY", TextFormat::RED);
	}

	public function alert($message){
		$this->send($message, \LogLevel::ALERT, "ALERT", TextFormat::RED);
	}

	public function critical($message){
		$this->send($message, \LogLevel::CRITICAL, "CRITICAL", TextFormat::RED);
	}

	public function error($message){
		$this->send($message, \LogLevel::ERROR, "ERROR", TextFormat::DARK_RED);
	}

	public function warning($message){
		$this->send($message, \LogLevel::WARNING, "WARNING", TextFormat::YELLOW);
	}

	public function notice($message){
		$this->send($message, \LogLevel::NOTICE, "NOTICE", TextFormat::AQUA);
	}

	public function info($message){
		$this->send($message, \LogLevel::INFO, "INFO", TextFormat::WHITE);
	}

	public function debug($message){
		if($this->logDebug === false){
			return;
		}
		$this->send($message, \LogLevel::DEBUG, "DEBUG", TextFormat::GRAY);
	}

	/**
	 * @param bool $logDebug
	 */
	public function setLogDebug(bool $logDebug){
		$this->logDebug = $logDebug;
	}

	public function logException(\Throwable $e, $trace = null){
		if($trace === null){
			$trace = $e->getTrace();
		}
		$errstr = $e->getMessage();
		$errfile = $e->getFile();
		$errno = $e->getCode();
		$errline = $e->getLine();

		$errorConversion = [
			0 => "EXCEPTION",
			E_ERROR => "E_ERROR",
			E_WARNING => "E_WARNING",
			E_PARSE => "E_PARSE",
			E_NOTICE => "E_NOTICE",
			E_CORE_ERROR => "E_CORE_ERROR",
			E_CORE_WARNING => "E_CORE_WARNING",
			E_COMPILE_ERROR => "E_COMPILE_ERROR",
			E_COMPILE_WARNING => "E_COMPILE_WARNING",
			E_USER_ERROR => "E_USER_ERROR",
			E_USER_WARNING => "E_USER_WARNING",
			E_USER_NOTICE => "E_USER_NOTICE",
			E_STRICT => "E_STRICT",
			E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
			E_DEPRECATED => "E_DEPRECATED",
			E_USER_DEPRECATED => "E_USER_DEPRECATED"
		];
		if($errno === 0){
			$type = LogLevel::CRITICAL;
		}else{
			$type = ($errno === E_ERROR or $errno === E_USER_ERROR) ? LogLevel::ERROR : (($errno === E_USER_WARNING or $errno === E_WARNING) ? LogLevel::WARNING : LogLevel::NOTICE);
		}
		$errno = $errorConversion[$errno] ?? $errno;
		$errstr = preg_replace('/\s+/', ' ', trim($errstr));
		$errfile = Utils::cleanPath($errfile);
		$this->log($type, get_class($e) . ": \"$errstr\" ($errno) in \"$errfile\" at line $errline");
		foreach(Utils::printableTrace($trace) as $i => $line){
			$this->log($type, $line);
		}
	}

	public function log($level, $message){
		switch($level){
			case LogLevel::EMERGENCY:
				$this->emergency($message);
				break;
			case LogLevel::ALERT:
				$this->alert($message);
				break;
			case LogLevel::CRITICAL:
				$this->critical($message);
				break;
			case LogLevel::ERROR:
				$this->error($message);
				break;
			case LogLevel::WARNING:
				$this->warning($message);
				break;
			case LogLevel::NOTICE:
				$this->notice($message);
				break;
			case LogLevel::INFO:
				$this->info($message);
				break;
			case LogLevel::DEBUG:
				$this->debug($message);
				break;
		}
	}

	public function shutdown(){
		$this->shutdown = true;
		$this->notify();
	}

	protected function send($message, $level, $prefix, $color){
		/** @var \DateTime|null $time */
		static $time = null;
		if($time === null){ //thread-local
			$time = new \DateTime('now', new \DateTimeZone($this->timezone));
		}
		$time->setTimestamp(time());

		$thread = \Thread::getCurrentThread();
		if($thread === null){
			$threadName = "Server thread";
		}elseif($thread instanceof Thread or $thread instanceof Worker){
			$threadName = $thread->getThreadName() . " thread";
		}else{
			$threadName = (new \ReflectionClass($thread))->getShortName() . " thread";
		}

		if(!Terminal::isInit()){
			Terminal::init($this->mainThreadHasFormattingCodes); //lazy-init colour codes because we don't know if they've been registered on this thread
		}

		$message = TextFormat::BOLD . TextFormat::BLUE . "[" . $time->format("H:i:s") . "] " . TextFormat::RESET . $color . "[" . $threadName . "/" . $prefix . "]:" . " " . $message . TextFormat::RESET;

		$this->synchronized(function() use ($message, $level, $time) : void{
			echo Terminal::toANSI($message) . PHP_EOL;

			if($this->attachment instanceof \ThreadedLoggerAttachment){
				$this->attachment->call($level, $message);
			}

			if($this->logToFile){
				$this->logStream[] = $time->format("Y-m-d") . " " . TextFormat::clean($message) . PHP_EOL;
			}
		});
	}

	/**
	 * @param resource $logResource
	 */
	private function writeLogStream($logResource){
		if(!$this->logToFile){
			return;
		}
		while($this->logStream->count() > 0){
			$chunk = $this->logStream->shift();
			fwrite($logResource, $chunk);
		}
	}

	public function run(){
		$this->shutdown = false;
		$logResource = fopen($this->logFile, "ab");
		if(!is_resource($logResource)){
			throw new \RuntimeException("Couldn't open log file");
		}

		while($this->shutdown === false){
			$this->writeLogStream($logResource);
			$this->synchronized(function(){
				$this->wait(25000);
			});
		}

		$this->writeLogStream($logResource);

		fclose($logResource);
	}
}
