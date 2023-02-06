<?php

declare(strict_types=1);

namespace pocketmine\utils;

use pocketmine\Thread;

use function getmypid;
use function time;

class ServerKiller extends Thread{

	public $time;

	public function __construct($time = 15){
		$this->time = $time;
	}

	public function run(){
		$start = time();
		$this->synchronized(function(){
			$this->wait($this->time * 1000000);
		});
		if(time() - $start >= $this->time){
			echo "\nTook too long to stop, server was killed forcefully!\n";
			@\pocketmine\kill(getmypid());
		}
	}

	public function getThreadName() : string{
		return "Server Killer";
	}
}
