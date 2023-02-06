<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

use function count;

class TextPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::TEXT_PACKET;

	public const COUNT_LIMIT = 10;

	public const TYPE_RAW = 0;
	public const TYPE_CHAT = 1;
	public const TYPE_TRANSLATION = 2;
	public const TYPE_POPUP = 3;
	public const TYPE_TIP = 4;
	public const TYPE_SYSTEM = 5;
	public const TYPE_WHISPER = 6;
	public const TYPE_ANNOUNCEMENT = 7;

	public $type;
	public $source;
	public $message;
	public $parameters = [];

	public function decodePayload(){
		$this->type = $this->getByte();
		switch($this->type){
			case self::TYPE_POPUP:
			case self::TYPE_CHAT:
			case self::TYPE_WHISPER:
			/** @noinspection PhpMissingBreakStatementInspection */
			case self::TYPE_ANNOUNCEMENT:
				$this->source = $this->getString();
			case self::TYPE_RAW:
			case self::TYPE_TIP:
			case self::TYPE_SYSTEM:
				$this->message = $this->getString();
				break;

			case self::TYPE_TRANSLATION:
				$this->message = $this->getString();
				$count = $this->getUnsignedVarInt();
				if($count > self::COUNT_LIMIT){
					throw new \UnexpectedValueException("Too many translation parameters count: $count");
				}
				for($i = 0; $i < $count; ++$i){
					$this->parameters[] = $this->getString();
				}
		}
	}

	public function encodePayload(){
		$this->putByte($this->type);
		switch($this->type){
			case self::TYPE_POPUP:
			case self::TYPE_CHAT:
			case self::TYPE_WHISPER:
			/** @noinspection PhpMissingBreakStatementInspection */
			case self::TYPE_ANNOUNCEMENT:
				$this->putString($this->source);
			case self::TYPE_RAW:
			case self::TYPE_TIP:
			case self::TYPE_SYSTEM:
				$this->putString($this->message);
				break;

			case self::TYPE_TRANSLATION:
				$this->putString($this->message);
				$this->putUnsignedVarInt(count($this->parameters));
				foreach($this->parameters as $p){
					$this->putString($p);
				}
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleText($this);
	}

}
