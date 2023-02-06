<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;

class BlockSpreadEvent extends BlockFormEvent{
	public static $handlerList = null;

	/** @var Block */
	private $source;

	public function __construct(Block $block, Block $source, Block $newState){
		parent::__construct($block, $newState);
		$this->source = $source;
	}

	/**
	 * @return Block
	 */
	public function getSource(){
		return $this->source;
	}

}