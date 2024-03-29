<?php

declare(strict_types=1);

namespace pocketmine;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLoadOrder;
use pocketmine\plugin\PluginManager;
use pocketmine\utils\Utils;
use pocketmine\utils\VersionString;
use pocketmine\utils\Zlib;
use raklib\RakLib;
use function base64_encode;
use function date;
use function error_get_last;
use function file;
use function file_exists;
use function file_get_contents;
use function fopen;
use function fwrite;
use function get_loaded_extensions;
use function implode;
use function is_dir;
use function is_resource;
use function json_encode;
use function max;
use function mkdir;
use function ob_end_clean;
use function ob_get_contents;
use function ob_start;
use function php_uname;
use function phpinfo;
use function phpversion;
use function preg_replace;
use function str_split;
use function strpos;
use function substr;
use function time;
use function zend_version;
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
use const FILE_IGNORE_NEW_LINES;
use const JSON_UNESCAPED_SLASHES;
use const PHP_EOL;
use const PHP_OS;
use const ZLIB_ENCODING_DEFLATE;

class CrashDump{

	/** @var Server */
	private $server;
	private $fp;
	private $time;
	private $data = [];
	/** @var string */
	private $encodedData = "";
	/** @var string */
	private $path;

	public function __construct(Server $server){
		$this->time = time();
		$this->server = $server;
		if(!is_dir($this->server->getDataPath() . "crashdumps")){
			mkdir($this->server->getDataPath() . "crashdumps");
		}
		$this->path = $this->server->getDataPath() . "crashdumps/" . date("D_M_j-H.i.s-T_Y", $this->time) . ".log";
		$this->fp = @fopen($this->path, "wb");
		if(!is_resource($this->fp)){
			throw new \RuntimeException("Could not create Crash Dump");
		}
		$this->data["time"] = $this->time;
		$this->addLine($this->server->getName() . " Crash Dump " . date("D M j H:i:s T Y", $this->time));
		$this->addLine();
		$this->baseCrash();
		$this->generalData();
		$this->pluginsData();
		$this->extraData();
		$this->encodeData();
	}

	public function getPath() : string{
		return $this->path;
	}

	public function getEncodedData(){
		return $this->encodedData;
	}

	public function getData() : array{
		return $this->data;
	}

	private function encodeData(){
		$this->addLine();
		$this->addLine("----------------------REPORT THE DATA BELOW THIS LINE-----------------------");
		$this->addLine();
		$this->addLine("===BEGIN CRASH DUMP===");
		$this->encodedData = Zlib::compress(json_encode($this->data, JSON_UNESCAPED_SLASHES), ZLIB_ENCODING_DEFLATE, 9);
		foreach(str_split(base64_encode($this->encodedData), 76) as $line){
			$this->addLine($line);
		}
		$this->addLine("===END CRASH DUMP===");
	}

	private function pluginsData(){
		if($this->server->getPluginManager() instanceof PluginManager){
			$this->addLine();
			$this->addLine("Loaded plugins:");
			$this->data["plugins"] = [];
			foreach($this->server->getPluginManager()->getPlugins() as $p){
				$d = $p->getDescription();
				$this->data["plugins"][$d->getName()] = [
					"name" => $d->getName(),
					"version" => $d->getVersion(),
					"authors" => $d->getAuthors(),
					"api" => $d->getCompatibleApis(),
					"enabled" => $p->isEnabled(),
					"depends" => $d->getDepend(),
					"softDepends" => $d->getSoftDepend(),
					"main" => $d->getMain(),
					"load" => $d->getOrder() === PluginLoadOrder::POSTWORLD ? "POSTWORLD" : ($d->getOrder() === PluginLoadOrder::PRESTARTUP ? "PRESTARTUP" : "STARTUP"),
					"website" => $d->getWebsite()
				];
				$this->addLine($d->getName() . " " . $d->getVersion() . " by " . implode(", ", $d->getAuthors()) . " for API(s) " . implode(", ", $d->getCompatibleApis()));
			}
		}
	}

	private function extraData(){
		global $arguments;

		if($this->server->getProperty("auto-report.send-settings", true) !== false){
			$this->data["parameters"] = (array) $arguments;
			$this->data["server.properties"] = @file_get_contents($this->server->getDataPath() . "server.properties");
			$this->data["server.properties"] = preg_replace("#^rcon\\.password=(.*)$#m", "rcon.password=******", $this->data["server.properties"]);
			$this->data["pocketmine.yml"] = @file_get_contents($this->server->getDataPath() . "pocketmine.yml");
		}else{
			$this->data["pocketmine.yml"] = "";
			$this->data["server.properties"] = "";
			$this->data["parameters"] = [];
		}
		$extensions = [];
		foreach(get_loaded_extensions() as $ext){
			$extensions[$ext] = phpversion($ext);
		}
		$this->data["extensions"] = $extensions;

		if($this->server->getProperty("auto-report.send-phpinfo", true) !== false){
			ob_start();
			phpinfo();
			$this->data["phpinfo"] = ob_get_contents();
			ob_end_clean();
		}
	}

	private function baseCrash(){
		global $lastExceptionError, $lastError;

		if(isset($lastExceptionError)){
			$error = $lastExceptionError;
		}else{
			$error = (array) error_get_last();
			$error["trace"] = getTrace(4); //Skipping CrashDump->baseCrash, CrashDump->construct, Server->crashDump
			$errorConversion = [
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
			$error["fullFile"] = $error["file"];
			$error["file"] = cleanPath($error["file"]);
			$error["type"] = $errorConversion[$error["type"]] ?? $error["type"];
			if(($pos = strpos($error["message"], "\n")) !== false){
				$error["message"] = substr($error["message"], 0, $pos);
			}
		}

		if(isset($lastError)){
			$this->data["lastError"] = $lastError;
		}

		$this->data["error"] = $error;
		unset($this->data["error"]["fullFile"]);
		unset($this->data["error"]["trace"]);
		$this->addLine("Error: " . $error["message"]);
		$this->addLine("File: " . $error["file"]);
		$this->addLine("Line: " . $error["line"]);
		$this->addLine("Type: " . $error["type"]);

		if(strpos($error["file"], "src/pocketmine/") === false and strpos($error["file"], "src/raklib/") === false and file_exists($error["fullFile"])){
			$this->addLine();
			$this->addLine("THIS CRASH WAS CAUSED BY A PLUGIN");
			$this->data["plugin"] = true;

			$reflection = new \ReflectionClass(PluginBase::class);
			$file = $reflection->getProperty("file");
			$file->setAccessible(true);
			foreach($this->server->getPluginManager()->getPlugins() as $plugin){
				$filePath = \pocketmine\cleanPath($file->getValue($plugin));
				if(strpos($error["file"], $filePath) === 0){
					$this->data["plugin"] = $plugin->getName();
					$this->addLine("BAD PLUGIN: " . $plugin->getDescription()->getFullName());
					break;
				}
			}
		}else{
			$this->data["plugin"] = false;
		}

		$this->addLine();
		$this->addLine("Code:");
		$this->data["code"] = [];

		if($this->server->getProperty("auto-report.send-code", true) !== false){
			$file = @file($error["fullFile"], FILE_IGNORE_NEW_LINES);
			for($l = max(0, $error["line"] - 10); $l < $error["line"] + 10; ++$l){
				$this->addLine("[" . ($l + 1) . "] " . @$file[$l]);
				$this->data["code"][$l + 1] = @$file[$l];
			}
		}

		$this->addLine();
		$this->addLine("Backtrace:");
		foreach(($this->data["trace"] = $error["trace"]) as $line){
			$this->addLine($line);
		}
		$this->addLine();
	}

	private function generalData(){
		$version = new VersionString();
		$this->data["general"] = [];
		$this->data["general"]["name"] = $this->server->getName();
		$this->data["general"]["version"] = $version->get(false);
		$this->data["general"]["build"] = $version->getBuild();
		$this->data["general"]["protocol"] = ProtocolInfo::CURRENT_PROTOCOL;
		$this->data["general"]["api"] = \pocketmine\API_VERSION;
		$this->data["general"]["git"] = \pocketmine\GIT_COMMIT;
		$this->data["general"]["raklib"] = RakLib::VERSION;
		$this->data["general"]["uname"] = php_uname("a");
		$this->data["general"]["php"] = phpversion();
		$this->data["general"]["zend"] = zend_version();
		$this->data["general"]["php_os"] = PHP_OS;
		$this->data["general"]["os"] = Utils::getOS();
		$this->addLine($this->server->getName() . " version: " . $version->get(false) . " #" . $version->getBuild() . " [Protocol " . ProtocolInfo::CURRENT_PROTOCOL . "; API " . API_VERSION . "]");
		$this->addLine("Git commit: " . GIT_COMMIT);
		$this->addLine("uname -a: " . php_uname("a"));
		$this->addLine("PHP Version: " . phpversion());
		$this->addLine("Zend version: " . zend_version());
		$this->addLine("OS : " . PHP_OS . ", " . Utils::getOS());
	}

	public function addLine($line = ""){
		fwrite($this->fp, $line . PHP_EOL);
	}

	public function add($str){
		fwrite($this->fp, $str);
	}

}