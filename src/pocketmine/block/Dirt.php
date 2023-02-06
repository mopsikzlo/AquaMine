<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Hoe;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\Player;

class Dirt extends Solid{

	protected $id = self::DIRT;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness(){
		return 0.5;
	}

	public function getToolType(){
		return Tool::TYPE_SHOVEL;
	}

	public function getName(){
		if($this->meta === 1){
			return "Coarse Dirt";
		}
		return "Dirt";
	}

	public function onActivate(Item $item, Player $player = null){
        if($item instanceof Hoe){
            $item->applyDamage(1);
            if($this->meta === 1){
                $this->getLevel()->setBlock($this, Block::get(Block::DIRT, 0), true);
            }else{
                $this->getLevel()->setBlock($this, Block::get(Block::FARMLAND, 0), true);
            }

			return true;
		}

		return false;
	}

	public function canBeActivated() : bool{
		return true;
	}
}