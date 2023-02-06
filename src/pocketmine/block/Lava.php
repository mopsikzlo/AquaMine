<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityCombustByBlockEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;

class Lava extends Liquid{

	protected $id = self::FLOWING_LAVA;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getLightLevel(){
		return 15;
	}

	public function getName(){
		return "Lava";
	}

	public function tickRate() : int{
		return 30;
	}

	public function getFlowDecayPerBlock() : int{
		return 2; //TODO: this is 1 in the nether
	}

	protected function checkForHarden(){
		$colliding = null;
		for($side = 1; $side <= 5; ++$side){ //don't check downwards side
			$blockSide = $this->getSide($side);
			if($blockSide instanceof Water){
				$colliding = $blockSide;
				break;
			}
		}
		if($colliding !== null){
			if($this->getDamage() === 0){
				$this->liquidCollide($colliding, Block::get(Block::OBSIDIAN));
			}elseif($this->getDamage() <= 4){
				$this->liquidCollide($colliding, Block::get(Block::COBBLESTONE));
			}
		}
	}

	protected function flowIntoBlock(Block $block, int $newFlowDecay) : void{
		if($block instanceof Water){
			$block->liquidCollide($this, Block::get(Block::STONE));
		}else{
			parent::flowIntoBlock($block, $newFlowDecay);
		}
	}

	public function onEntityCollide(Entity $entity){
		$entity->fallDistance *= 0.5;

		$ev = new EntityDamageByBlockEvent($this, $entity, EntityDamageEvent::CAUSE_LAVA, 4);
		$entity->attack($ev->getFinalDamage(), $ev);

		$ev = new EntityCombustByBlockEvent($this, $entity, 15);
		$ev->call();
		if(!$ev->isCancelled()){
			$entity->setOnFire($ev->getDuration());
		}

		$entity->resetFallDistance();
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$ret = $this->getLevel()->setBlock($this, $this, true, false);
		$this->getLevel()->scheduleDelayedBlockUpdate($this, $this->tickRate());

		return $ret;
	}

}
