<?php

declare(strict_types=1);

namespace pocketmine\permission;

use pocketmine\Server;
use pocketmine\utils\MainLogger;

use function fclose;
use function fgets;
use function fopen;
use function fwrite;
use function is_resource;
use function strftime;
use function strtolower;
use function time;

class BanList{

	/** @var BanEntry[] */
	private $list = [];

	/** @var string */
	private $file;

	/** @var bool */
	private $enabled = true;

	/**
	 * @param string $file
	 */
	public function __construct(string $file){
		$this->file = $file;
	}

	/**
	 * @return bool
	 */
	public function isEnabled() : bool{
		return $this->enabled === true;
	}

	/**
	 * @param bool $flag
	 */
	public function setEnabled(bool $flag){
		$this->enabled = $flag;
	}

	/**
	 * @return BanEntry[]
	 */
	public function getEntries() : array{
		$this->removeExpired();

		return $this->list;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isBanned(string $name) : bool{
		$name = strtolower($name);
		if(!$this->isEnabled()){
			return false;
		}else{
			$this->removeExpired();

			return isset($this->list[$name]);
		}
	}

	/**
	 * @param BanEntry $entry
	 */
	public function add(BanEntry $entry){
		$this->list[$entry->getName()] = $entry;
		$this->save();
	}

	/**
	 * @param string    $target
	 * @param string    $reason
	 * @param \DateTime $expires
	 * @param string    $source
	 *
	 * @return BanEntry
	 */
	public function addBan(string $target, string $reason = null, \DateTime $expires = null, string $source = null) : BanEntry{
		$entry = new BanEntry($target);
		$entry->setSource($source != null ? $source : $entry->getSource());
		$entry->setExpires($expires);
		$entry->setReason($reason != null ? $reason : $entry->getReason());

		$this->list[$entry->getName()] = $entry;
		$this->save();

		return $entry;
	}

	/**
	 * @param string $name
	 */
	public function remove(string $name){
		$name = strtolower($name);
		if(isset($this->list[$name])){
			unset($this->list[$name]);
			$this->save();
		}
	}

	public function removeExpired(){
		foreach($this->list as $name => $entry){
			if($entry->hasExpired()){
				unset($this->list[$name]);
			}
		}
	}

	public function load(){
		$this->list = [];
		$fp = @fopen($this->file, "r");
		if(is_resource($fp)){
			while(($line = fgets($fp)) !== false){
				if($line{0} !== "#"){
					$entry = BanEntry::fromString($line);
					if($entry instanceof BanEntry){
						$this->list[$entry->getName()] = $entry;
					}
				}
			}
			fclose($fp);
		}else{
			MainLogger::getLogger()->error("Could not load ban list");
		}
	}

	/**
	 * @param bool $flag
	 */
	public function save(bool $flag = true){
		$this->removeExpired();
		$fp = @fopen($this->file, "w");
		if(is_resource($fp)){
			if($flag === true){
				fwrite($fp, "# Updated " . strftime("%x %H:%M", time()) . " by " . Server::getInstance()->getName() . " " . Server::getInstance()->getPocketMineVersion() . "\n");
				fwrite($fp, "# victim name | ban date | banned by | banned until | reason\n\n");
			}

			foreach($this->list as $entry){
				fwrite($fp, $entry->getString() . "\n");
			}
			fclose($fp);
		}else{
			MainLogger::getLogger()->error("Could not save ban list");
		}
	}

}