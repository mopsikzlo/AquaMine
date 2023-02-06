<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

use pocketmine\network\mcpe\NetworkBinaryStream;

final class PlayerMovementSettings{
	/** @var int */
	private $movementType;
	/** @var int */
	private $rewindHistorySize;
	/** @var bool */
	private $serverAuthoritativeBlockBreaking;

	public function __construct(int $movementType, int $rewindHistorySize, bool $serverAuthoritativeBlockBreaking){
		$this->movementType = $movementType;
		$this->rewindHistorySize = $rewindHistorySize;
		//do not ask me what the F this is doing here
		$this->serverAuthoritativeBlockBreaking = $serverAuthoritativeBlockBreaking;
	}

	public function getMovementType() : int{
		return $this->movementType;
	}

	public function getRewindHistorySize() : int{
		return $this->rewindHistorySize;
	}

	public function isServerAuthoritativeBlockBreaking() : bool{
		return $this->serverAuthoritativeBlockBreaking;
	}

	public static function read(NetworkBinaryStream $in) : self{
		$movementType = $in->getVarInt();
		$rewindHistorySize = $in->getVarInt();
		$serverAuthBlockBreaking = $in->getBool();
		return new self($movementType, $rewindHistorySize, $serverAuthBlockBreaking);
	}

	public function write(NetworkBinaryStream $out) : void{
		$out->putVarInt($this->movementType);
		$out->putVarInt($this->rewindHistorySize);
		$out->putBool($this->serverAuthoritativeBlockBreaking);
	}
}