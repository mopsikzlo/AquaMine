<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

class EnchantmentInstance{

	/** @var int */
	public $type;
	/** @var int */
	public $level;

	public function __construct(int $type = -1, int $level = -1){
		$this->type = $type;
		$this->level = $level;
	}
}