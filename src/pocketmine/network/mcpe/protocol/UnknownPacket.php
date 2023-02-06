<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol;


use pocketmine\network\NetworkSession;

use function ord;
use function strlen;

class UnknownPacket extends DataPacket{
	public const NETWORK_ID = -1; //Invalid, do not try to write this

	public $payload;

	public function pid(){
		if(strlen($this->payload ?? "") > 0){
			return ord($this->payload{0});
		}
		return self::NETWORK_ID;
	}

	public function getName() : string{
		return "unknown packet";
	}

	public function decode(){
		$this->payload = $this->getRemaining();
	}

	public function encode(){
		//Do not reset the buffer, this class does not have a valid NETWORK_ID constant.
		$this->put($this->payload);
	}

	public function handle(NetworkSession $session) : bool{
		return false;
	}
}