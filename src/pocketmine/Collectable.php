<?php

declare(strict_types=1);

namespace pocketmine;

abstract class Collectable extends \Threaded implements \Collectable{

	private $isGarbage = false;

	public function isGarbage() : bool{
		return $this->isGarbage;
	}

	public function setGarbage(){
		$this->isGarbage = true;
	}
}
