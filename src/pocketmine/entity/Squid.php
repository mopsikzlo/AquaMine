<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\Player;

use function atan2;
use function mt_rand;
use function sqrt;

use const M_PI;

class Squid extends WaterAnimal{
	public const NETWORK_ID = self::SQUID;

	public $width = 0.95;
	public $height = 0.95;

	/** @var Vector3 */
	public $swimDirection = null;
	public $swimSpeed = 0.1;

	private $switchDirectionTicker = 0;

	public function initEntity(){
		$this->setMaxHealth(10);
		parent::initEntity();
	}

	public function getName(){
		return "Squid";
	}

	public function attack($damage, EntityDamageEvent $source){
		parent::attack($damage, $source);
		if($source->isCancelled()){
			return;
		}

		if($source instanceof EntityDamageByEntityEvent){
			$this->swimSpeed = mt_rand(150, 350) / 2000;
			$e = $source->getDamager();
			if($e !== null){
				$this->swimDirection = (new Vector3($this->x - $e->x, $this->y - $e->y, $this->z - $e->z))->normalize();
			}

			$pk = new EntityEventPacket();
			$pk->entityRuntimeId = $this->getId();
			$pk->event = EntityEventPacket::SQUID_INK_CLOUD;
			$this->server->broadcastPacket($this->hasSpawned, $pk);
		}
	}

	private function generateRandomDirection(){
		return new Vector3(mt_rand(-1000, 1000) / 1000, mt_rand(-500, 500) / 1000, mt_rand(-1000, 1000) / 1000);
	}

	public function entityBaseTick($tickDiff = 1){
		if($this->closed){
			return false;
		}

		if(++$this->switchDirectionTicker === 100){
			$this->switchDirectionTicker = 0;
			if(mt_rand(0, 100) < 50){
				$this->swimDirection = null;
			}
		}

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->isAlive()){

			if($this->y > 62 and $this->swimDirection !== null){
				$this->swimDirection->y = -0.5;
			}

			$inWater = $this->isInsideOfWater();
			if(!$inWater){
				$this->motionY -= $this->gravity;
				$this->swimDirection = null;
			}elseif($this->swimDirection !== null){
				if($this->motionX ** 2 + $this->motionY ** 2 + $this->motionZ ** 2 <= $this->swimDirection->lengthSquared()){
					$this->motionX = $this->swimDirection->x * $this->swimSpeed;
					$this->motionY = $this->swimDirection->y * $this->swimSpeed;
					$this->motionZ = $this->swimDirection->z * $this->swimSpeed;
				}
			}else{
				$this->swimDirection = $this->generateRandomDirection();
				$this->swimSpeed = mt_rand(50, 100) / 2000;
			}

			$f = sqrt(($this->motionX ** 2) + ($this->motionZ ** 2));
			$this->yaw = (-atan2($this->motionX, $this->motionZ) * 180 / M_PI);
			$this->pitch = (-atan2($f, $this->motionY) * 180 / M_PI);
		}

		return $hasUpdate;
	}

	public function getDrops() : array{
		return [
			ItemItem::get(ItemItem::DYE, 0, mt_rand(1, 3))
		];
	}
}
