<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\BedrockPlayer;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\Lava;
use pocketmine\block\Liquid;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\level\Level;
use pocketmine\Player;

class Bucket extends Item{

	public const MILK = 1;

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::BUCKET, $meta, $count, "Bucket");
	}

	public function getMaxStackSize(){
		return 1;
	}

	public function onActivate(Level $level, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		$targetBlock = Block::get($this->meta);

		if($targetBlock instanceof Air){
			if($target instanceof Liquid and $target->getDamage() === 0){
				$result = clone $this;
				$result->setDamage($target->getId());
				$ev = new PlayerBucketFillEvent($player, $block, $face, $this, $result);
				$ev->call();
				if(!$ev->isCancelled()){
					$player->getLevel()->setBlock($target, new Air(), true, true);
					if($target instanceof Lava){
						$soundId = LevelSoundEventPacket::SOUND_BUCKET_FILL_LAVA;
 					}else{
 						$soundId = LevelSoundEventPacket::SOUND_BUCKET_FILL_WATER;
 					}
 					$target->getLevel()->broadcastLevelSoundEvent($target, $soundId);
					if($player->isSurvival()){
						$player->getInventory()->setItemInHand($ev->getItem());
					}
					return true;
				}else{
					$player->getInventory()->sendContents($player);
				}
			}
		}elseif($targetBlock instanceof Liquid){
			$result = clone $this;
			$result->setDamage(0);

			$ev = new PlayerBucketEmptyEvent($player, $block, $face, $this, $result);
			$ev->call();
			if(!$ev->isCancelled()){
				$player->getLevel()->setBlock($block, $targetBlock, true, true);
				if($targetBlock instanceof Lava){
 					$soundId = LevelSoundEventPacket::SOUND_BUCKET_EMPTY_LAVA;
 				}else{
 					$soundId = LevelSoundEventPacket::SOUND_BUCKET_EMPTY_WATER;
 				}
 				$targetBlock->getLevel()->broadcastLevelSoundEvent($targetBlock, $soundId);
				if($player->isSurvival()){
					$player->getInventory()->setItemInHand($ev->getItem());
				}
				return true;
			}else{
				$player->getInventory()->sendContents($player);
			}
		}

		return false;
	}

	public function getResidue(){
		return Item::get(Item::BUCKET, 0, 1);
	}

	public function canBeConsumed() : bool{
		return $this->meta === self::MILK;
	}

	public function onConsume(Entity $human){
		$human->level->broadcastLevelSoundEvent($human->add(0, $human->getEyeHeight(), 0), LevelSoundEventPacket::SOUND_BURP);
		$human->removeAllEffects();

		$human->getInventory()->setItemInHand($this->getResidue());
	}

	public function onReleaseUsing(Player $player) : void{
		if(!$player instanceof BedrockPlayer and $this->canBeConsumed()){ //Blame Mojang
			$this->onConsume($player);
		}
	}
}