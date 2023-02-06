<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\BedrockPlayer;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\bedrock\adapter\ProtocolAdapterFactory;
use pocketmine\network\bedrock\PacketTranslator;
use pocketmine\network\bedrock\palette\BlockPalette;
use pocketmine\network\bedrock\protocol\SetActorDataPacket as BedrockSetActorDataPacket;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\Player;

use function is_array;

class FallingSand extends Entity{
	public const NETWORK_ID = self::FALLING_BLOCK;

	public $width = 0.98;
	public $height = 0.98;

	protected $baseOffset = 0.49;

	public $gravity = 0.04;
	public $drag = 0.02;

	protected $blockId = 0;
	protected $damage;

	public $canCollide = false;

	protected function initEntity(){
		parent::initEntity();
		if($this->namedtag->hasTag("TileID", IntTag::class)){
			$this->blockId = $this->namedtag->getInt("TileID");
		}elseif($this->namedtag->hasTag("Tile", IntTag::class)){
			$this->blockId = $this->namedtag->getInt("Tile");
			$this->namedtag->setInt("TileID", $this->blockId);
			$this->namedtag->removeTag("Tile");
		}

		if($this->namedtag->hasTag("Data", ByteTag::class)){
			$this->damage = $this->namedtag->getByte("Data");
		}

		if($this->blockId === 0){
			$this->close();
			return;
		}

		$this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $this->getBlock() | ($this->getDamage() << 8));
	}

	public function canCollideWith(Entity $entity){
		return false;
	}

	public function attack($damage, EntityDamageEvent $source){
		if($source->getCause() === EntityDamageEvent::CAUSE_VOID){
			parent::attack($damage, $source);
		}
	}

	public function entityBaseTick($tickDiff = 1){
		if($this->closed){
			return false;
		}

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if(!$this->isFlaggedForDespawn() and $this->onGround){
			$this->flagForDespawn();

			$pos = $this->add(-$this->width / 2, $this->height, -$this->width / 2)->floor();

			$block = $this->level->getBlockAt($pos->x, $pos->y, $pos->z);

			if($block->getId() > 0 and $block->isTransparent() and !$block->canBeReplaced()){
				//FIXME: anvils are supposed to destroy torches
				$this->getLevel()->dropItem($this, ItemItem::get($this->getBlock(), $this->getDamage(), 1));
			}else{
				$ev = new EntityBlockChangeEvent($this, $block, Block::get($this->getBlock(), $this->getDamage()));
				$ev->call();
				if(!$ev->isCancelled()){
					$this->getLevel()->setBlock($pos, $ev->getTo(), true);
				}
			}
			$hasUpdate = true;
		}

		return $hasUpdate;
	}

	public function getBlock(){
		return $this->blockId;
	}

	public function getDamage(){
		return $this->damage;
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->setInt("TileID", $this->blockId);
		$this->namedtag->setByte("Data", $this->damage);
	}

	protected function sendSpawnPacket(Player $player) : void{
		$pk = new AddEntityPacket();
		$pk->type = static::NETWORK_ID;
		$pk->entityRuntimeId = $this->getId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		if($player instanceof BedrockPlayer and isset($pk->metadata[self::DATA_VARIANT])){
			$adapter = ProtocolAdapterFactory::get($player->getProtocolVersion());
			if($adapter !== null){
				$pk->metadata[self::DATA_VARIANT][1] = $adapter->translateBlockId(BlockPalette::getRuntimeFromLegacyId($this->blockId, $this->damage));
			}else{
				$pk->metadata[self::DATA_VARIANT][1] = BlockPalette::getRuntimeFromLegacyId($this->blockId, $this->damage);
			}
		}

		$player->sendDataPacket($pk);
	}

	/**
	 * @param Player[]|Player $player
	 * @param array           $data Properly formatted entity data, defaults to everything
	 */
	public function sendData($player, array $data = null){
		if(!is_array($player)){
			$player = [$player];
		}

		$bedrockPackets = [];
		foreach($player as $p){
			if($p !== $this and $p instanceof BedrockPlayer){
				$bedrockPackets[$p->getProtocolVersion()] = null;
			}
		}
		if($this instanceof BedrockPlayer){
			$bedrockPackets[$this->getProtocolVersion()] = null;
		}

		$pk = new SetEntityDataPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->metadata = $data ?? $this->dataProperties;

		$bk = new BedrockSetActorDataPacket();
		$bk->actorRuntimeId = $this->getId();
		$bk->metadata = PacketTranslator::translateMetadata($pk->metadata);

		foreach($bedrockPackets as $protocol => &$bbk){
			$bbk = clone $bk;
			if(isset($bk->metadata[self::DATA_VARIANT])){
				$adapter = ProtocolAdapterFactory::get($protocol);
				if($adapter !== null){
					$bbk->metadata[self::DATA_VARIANT][1] = $adapter->translateBlockId(BlockPalette::getRuntimeFromLegacyId($this->blockId, $this->damage));
				}else{
					$bbk->metadata[self::DATA_VARIANT][1] = BlockPalette::getRuntimeFromLegacyId($this->blockId, $this->damage);
				}
			}
		}

		foreach($player as $p){
			if($p === $this){
				continue;
			}
			$p->sendDataPacket($p instanceof BedrockPlayer ? clone $bedrockPackets[$p->getProtocolVersion()] : clone $pk);
		}

		if($this instanceof Player){
			$this->sendDataPacket($this instanceof BedrockPlayer ? $bedrockPackets[$p->getProtocolVersion()] : $pk);
		}
	}
}
