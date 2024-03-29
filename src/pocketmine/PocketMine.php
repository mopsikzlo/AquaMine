<?php

declare(strict_types=1);

namespace {
	const INT32_MIN = -0x80000000;
	const INT32_MAX = 0x7fffffff;
}

namespace pocketmine {
	use pocketmine\utils\Binary;
	use pocketmine\utils\MainLogger;
	use pocketmine\utils\Terminal;
	use pocketmine\utils\Utils;
	use pocketmine\wizard\SetupWizard;
	use raklib\RakLib;

	const NAME = "AquaMine";
	const VERSION = "1.3.25";
	const API_VERSION = "3.3.0";
	const CODENAME = "Blame Mojang";

	/*
	 * Startup code. Do not look at it, it may harm you.
	 * Most of them are hacks to fix date-related bugs, or basic functions used after this
	 * This is the only non-class based file on this project.
	 * Enjoy it as much as I did writing it. I don't want to do it again.
	 */

	if(version_compare("7.2", PHP_VERSION) > 0){
		echo "[CRITICAL] You must use PHP >= 7.2" . PHP_EOL;
		exit(1);
	}

	if(!extension_loaded("pthreads")){
		echo "[CRITICAL] Unable to find the pthreads extension." . PHP_EOL;
		exit(1);
	}

	if(!extension_loaded("igbinary")){
		echo "[CRITICAL] Unable to find the igbinary extension." . PHP_EOL;
		exit(1);
	}

	error_reporting(-1);

	if(!extension_loaded("phar")){
		echo "[CRITICAL] Unable to find the Phar extension." . PHP_EOL;
		exit(1);
	}

	if(\Phar::running(true) !== ""){
		define('pocketmine\PATH', \Phar::running(true) . "/");
	}else{
		define('pocketmine\PATH', realpath(getcwd()) . DIRECTORY_SEPARATOR);
	}

	if(!class_exists("ClassLoader", false)){
		if(!is_file(\pocketmine\PATH . "src/spl/ClassLoader.php")){
			echo "[CRITICAL] Unable to find the PocketMine-SPL library." . PHP_EOL;
			echo "[CRITICAL] Please use provided builds or clone the repository recursively." . PHP_EOL;
			exit(1);
		}
		require_once(\pocketmine\PATH . "src/spl/ClassLoader.php");
		require_once(\pocketmine\PATH . "src/spl/BaseClassLoader.php");
	}

	$autoloader = new \BaseClassLoader();
	$autoloader->addPath(\pocketmine\PATH . "src");
	$autoloader->addPath(\pocketmine\PATH . "src" . DIRECTORY_SEPARATOR . "spl");
	$autoloader->register(true);

	//set this after the autoloader is registered
	set_error_handler([Utils::class, 'errorExceptionHandler']);

	if(!class_exists(RakLib::class)){
		echo "[CRITICAL] Unable to find the RakLib library." . PHP_EOL;
		echo "[CRITICAL] Please use provided builds or clone the repository recursively." . PHP_EOL;
		exit(1);
	}

	set_time_limit(0); //Who set it to 30 seconds?!?!

	ini_set("allow_url_fopen", '1');
	ini_set("display_errors", '1');
	ini_set("display_startup_errors", '1');
	ini_set("default_charset", "utf-8");

	ini_set("memory_limit", '-1');
	define('pocketmine\START_TIME', microtime(true));

	$opts = getopt("", ["data:", "plugins:", "no-wizard", "enable-profiler"]);

	define('pocketmine\DATA', isset($opts["data"]) ? $opts["data"] . DIRECTORY_SEPARATOR : \realpath(\getcwd()) . DIRECTORY_SEPARATOR);
	define('pocketmine\PLUGIN_PATH', isset($opts["plugins"]) ? $opts["plugins"] . DIRECTORY_SEPARATOR : \realpath(\getcwd()) . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR);

	Terminal::init();

	define('pocketmine\ANSI', Terminal::hasFormattingCodes());

	if(!file_exists(\pocketmine\DATA)){
		mkdir(\pocketmine\DATA, 0777, true);
	}

	//Logger has a dependency on timezone, so we'll set it to UTC until we can get the actual timezone.
	date_default_timezone_set("UTC");

	$logger = new MainLogger(\pocketmine\DATA . "server.log");
	$logger->registerStatic();

	if(!ini_get("date.timezone")){
		if(($timezone = detect_system_timezone()) and date_default_timezone_set($timezone)){
			//Success! Timezone has already been set and validated in the if statement.
			//This here is just for redundancy just in case some program wants to read timezone data from the ini.
			ini_set("date.timezone", $timezone);
		}else{
			//If system timezone detection fails or timezone is an invalid value.
			if($response = Utils::getURL("http://ip-api.com/json")
				and $ip_geolocation_data = json_decode($response, true)
				and $ip_geolocation_data['status'] !== 'fail'
				and date_default_timezone_set($ip_geolocation_data['timezone'])
			){
				//Again, for redundancy.
				ini_set("date.timezone", $ip_geolocation_data['timezone']);
			}else{
				ini_set("date.timezone", "UTC");
				date_default_timezone_set("UTC");
				$logger->warning("Timezone could not be automatically determined. An incorrect timezone will result in incorrect timestamps on console logs. It has been set to \"UTC\" by default. You can change it on the php.ini file.");
			}
		}
	}else{
		/*
		 * This is here so that people don't come to us complaining and fill up the issue tracker when they put
		 * an incorrect timezone abbreviation in php.ini apparently.
		 */
		$timezone = ini_get("date.timezone");
		if(strpos($timezone, "/") === false){
			$default_timezone = timezone_name_from_abbr($timezone);
			if($default_timezone !== false){
				ini_set("date.timezone", $default_timezone);
				date_default_timezone_set($default_timezone);
			}else{
				$logger->warning("Timezone \"$timezone\" could not be parsed as a valid timezone from php.ini, falling back to auto-detection");
			}
		}else{
			date_default_timezone_set($timezone);
		}
	}

	function detect_system_timezone(){
		switch(Utils::getOS()){
			case 'win':
				$regex = '/(UTC)(\+*\-*\d*\d*\:*\d*\d*)/';

				/*
				 * wmic timezone get Caption
				 * Get the timezone offset
				 *
				 * Sample Output var_dump
				 * array(3) {
				 *	  [0] =>
				 *	  string(7) "Caption"
				 *	  [1] =>
				 *	  string(20) "(UTC+09:30) Adelaide"
				 *	  [2] =>
				 *	  string(0) ""
				 *	}
				 */
				exec("wmic timezone get Caption", $output);

				$string = trim(implode("\n", $output));

				//Detect the Time Zone string
				preg_match($regex, $string, $matches);

				if(!isset($matches[2])){
					return false;
				}

				$offset = $matches[2];

				if($offset == ""){
					return "UTC";
				}

				return parse_offset($offset);
			case 'linux':
				// Ubuntu / Debian.
				if(file_exists('/etc/timezone')){
					$data = file_get_contents('/etc/timezone');
					if($data){
						return trim($data);
					}
				}

				// RHEL / CentOS
				if(file_exists('/etc/sysconfig/clock')){
					$data = parse_ini_file('/etc/sysconfig/clock');
					if(!empty($data['ZONE'])){
						return trim($data['ZONE']);
					}
				}

				//Portable method for incompatible linux distributions.

				$offset = trim(exec('date +%:z'));

				if($offset == "+00:00"){
					return "UTC";
				}

				return parse_offset($offset);
			case 'mac':
				if(is_link('/etc/localtime')){
					$filename = readlink('/etc/localtime');
					if(strpos($filename, '/usr/share/zoneinfo/') === 0){
						$timezone = substr($filename, 20);
						return trim($timezone);
					}
				}

				return false;
			default:
				return false;
		}
	}

	/**
	 * @param string $offset In the format of +09:00, +02:00, -04:00 etc.
	 *
	 * @return string|bool
	 */
	function parse_offset($offset){
		//Make signed offsets unsigned for date_parse
		if(strpos($offset, '-') !== false){
			$negative_offset = true;
			$offset = str_replace('-', '', $offset);
		}else{
			if(strpos($offset, '+') !== false){
				$negative_offset = false;
				$offset = str_replace('+', '', $offset);
			}else{
				return false;
			}
		}

		$parsed = date_parse($offset);
		$offset = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];

		//After date_parse is done, put the sign back
		if($negative_offset == true){
			$offset = -abs($offset);
		}

		//And then, look the offset up.
		//timezone_name_from_abbr is not used because it returns false on some(most) offsets because it's mapping function is weird.
		//That's been a bug in PHP since 2008!
		foreach(timezone_abbreviations_list() as $zones){
			foreach($zones as $timezone){
				if($timezone['offset'] == $offset){
					return $timezone['timezone_id'];
				}
			}
		}

		return false;
	}

	if(isset($opts["enable-profiler"])){
		if(function_exists("profiler_enable")){
			\profiler_enable();
			$logger->notice("Execution is being profiled");
		}else{
			$logger->notice("No profiler found. Please install https://github.com/krakjoe/profiler");
		}
	}

	function kill($pid){
		switch(Utils::getOS()){
			case "win":
				exec("taskkill.exe /F /PID " . ((int) $pid) . " > NUL");
				break;
			case "mac":
			case "linux":
			default:
				if(function_exists("posix_kill")){
					posix_kill($pid, 9); //SIGKILL
				}else{
					exec("kill -9 " . ((int) $pid) . " > /dev/null 2>&1");
				}
		}
	}

	/**
	 * @param object $value
	 * @param bool   $includeCurrent
	 *
	 * @return int
	 */
	function getReferenceCount($value, $includeCurrent = true){
		ob_start();
		debug_zval_dump($value);
		$ret = explode("\n", ob_get_contents());
		ob_end_clean();

		if(count($ret) >= 1 and preg_match('/^.* refcount\\(([0-9]+)\\)\\{$/', trim($ret[0]), $m) > 0){
			return ((int) $m[1]) - ($includeCurrent ? 3 : 4); //$value + zval call + extra call
		}
		return -1;
	}

	function getTrace($start = 0, $trace = null){
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

				$params = implode(", ", array_map(function($value){
					return (is_object($value) ? get_class($value) . " object" : gettype($value) . " " . (is_array($value) ? "Array()" : Utils::printable(@strval($value))));
				}, $args));
			}
			$messages[] = "#$j " . (isset($trace[$i]["file"]) ? cleanPath($trace[$i]["file"]) : "") . "(" . ($trace[$i]["line"] ?? "") . "): " . (isset($trace[$i]["class"]) ? $trace[$i]["class"] . (($trace[$i]["type"] === "dynamic" or $trace[$i]["type"] === "->") ? "->" : "::") : "") . $trace[$i]["function"] . "(" . Utils::printable($params) . ")";
		}

		return $messages;
	}

	function cleanPath($path){
		return str_replace(["\\", ".php", "phar://", str_replace(["\\", "phar://"], ["/", ""], \pocketmine\PATH), str_replace(["\\", "phar://"], ["/", ""], \pocketmine\PLUGIN_PATH)], ["/", "", "", "", ""], $path);
	}

	$exitCode = 0;

	do{
		$errors = 0;

		if(PHP_INT_SIZE < 8){
			$logger->critical("Running PocketMine-MP with 32-bit systems/PHP is no longer supported. Please upgrade to a 64-bit system or use a 64-bit PHP binary.");
			$exitCode = 1;
			break;
		}

		if(php_sapi_name() !== "cli"){
			$logger->critical("You must run PocketMine-MP using the CLI.");
			++$errors;
		}

		$pthreads_version = phpversion("pthreads");
		if(substr_count($pthreads_version, ".") < 2){
			$pthreads_version = "0.$pthreads_version";
		}
		if(version_compare($pthreads_version, "3.1.7dev") < 0){
			$logger->critical("pthreads >= 3.1.7dev is required, while you have $pthreads_version.");
			++$errors;
		}

		if(extension_loaded("leveldb")){
			$leveldb_version = phpversion("leveldb");
			if(version_compare($leveldb_version, "0.2.0") < 0){
				$logger->critical("php-leveldb >= 0.2.0 is required, while you have $leveldb_version");
				++$errors;
			}
		}

		if(extension_loaded("pocketmine")){
			if(version_compare(phpversion("pocketmine"), "0.0.1") < 0){
				$logger->critical("You have the native PocketMine extension, but your version is lower than 0.0.1.");
				++$errors;
			}elseif(version_compare(phpversion("pocketmine"), "0.0.4") > 0){
				$logger->critical("You have the native PocketMine extension, but your version is higher than 0.0.4.");
				++$errors;
			}
		}

		if(extension_loaded("xdebug")){
			$logger->warning(PHP_EOL . PHP_EOL . PHP_EOL . "\tYou are running PocketMine with xdebug enabled. This has a major impact on performance." . PHP_EOL . PHP_EOL);
		}

		$extensions = [
			"curl" => "cURL",
			"json" => "JSON",
			"mbstring" => "Multibyte String",
			"yaml" => "YAML",
			"sockets" => "Sockets",
			"zip" => "Zip",
			"zlib" => "Zlib"
		];

		foreach($extensions as $ext => $name){
			if(!extension_loaded($ext)){
				$logger->critical("Unable to find the $name ($ext) extension.");
				++$errors;
			}
		}

		if($errors > 0){
			$logger->critical("Please use the installer provided on the homepage, or recompile PHP again.");
			$exitCode = 1;
			break;
		}

		$gitHash = str_repeat("00", 20);
#ifndef COMPILE
		if(\Phar::running(true) === ""){
			if(Utils::execute("git rev-parse HEAD", $out) === 0){
				$gitHash = trim($out);
				if(Utils::execute("git diff --quiet") === 1 or Utils::execute("git diff --cached --quiet") === 1){ //Locally-modified
					$gitHash .= "-dirty";
				}
			}
		}
#endif
		define('pocketmine\GIT_COMMIT', $gitHash);


		@define("ENDIANNESS", (pack("d", 1) === "\77\360\0\0\0\0\0\0" ? Binary::BIG_ENDIAN : Binary::LITTLE_ENDIAN));
		@define("INT32_MASK", is_int(0xffffffff) ? 0xffffffff : -1);
		@ini_set("opcache.mmap_base", bin2hex(random_bytes(8))); //Fix OPCache address errors


		if(!file_exists(\pocketmine\DATA . "server.properties") and !isset($opts["no-wizard"])){
			$installer = new SetupWizard();
			if(!$installer->run()){
				$exitCode = -1;
				break;
			}
		}


		if(\Phar::running(true) === ""){
			$logger->warning("Non-packaged PocketMine-MP installation detected, do not use on production.");
		}

		ThreadManager::init();
		new Server($autoloader, $logger, \pocketmine\PATH, \pocketmine\DATA, \pocketmine\PLUGIN_PATH);

		$logger->info("Stopping other threads");

		$erroredThreads = 0;
		foreach(ThreadManager::getInstance()->getAll() as $id => $thread){
			$logger->debug("Stopping " . $thread->getThreadName() . " thread");
			try{
				$thread->quit();
				$logger->debug($thread->getThreadName() . " thread stopped successfully.");
			}catch(\ThreadException $e){
				++$erroredThreads;
				$logger->debug("Could not stop " . $thread->getThreadName() . " thread: " . $e->getMessage());
			}
		}

		if($erroredThreads > 0){
			if(\pocketmine\DEBUG > 1){
				echo "Some threads could not be stopped, performing a force-kill" . PHP_EOL . PHP_EOL;
			}
			kill(getmypid());
		}
	}while(false);

	$logger->shutdown();
	$logger->join();

	echo Terminal::$FORMAT_RESET . PHP_EOL;

	exit($exitCode);
}
