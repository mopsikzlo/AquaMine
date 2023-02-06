<?php

declare(strict_types=1);

namespace pocketmine\nbt;

use Closure;

class ReaderTracker{

	/** @var int */
	private $maxDepth;
	/** @var int */
	private $currentDepth = 0;

	public function __construct(int $maxDepth){
		$this->maxDepth = $maxDepth;
	}

	/**
	 * @param Closure $execute
	 */
	public function protectDepth(Closure $execute) : void{
		if($this->maxDepth > 0 and ++$this->currentDepth > $this->maxDepth){
			throw new NbtDataException("Nesting level too deep: reached max depth of $this->maxDepth tags");
		}
		try{
			$execute();
		}finally{
			--$this->currentDepth;
		}
	}
}
