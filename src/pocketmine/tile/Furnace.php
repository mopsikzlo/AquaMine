<?php

declare(strict_types=1);

namespace pocketmine\tile;

use pocketmine\block\Block;
use pocketmine\event\inventory\FurnaceBurnEvent;
use pocketmine\event\inventory\FurnaceSmeltEvent;
use pocketmine\inventory\FurnaceInventory;
use pocketmine\inventory\FurnaceRecipe;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;

use function ceil;
use function microtime;

class Furnace extends Spawnable implements InventoryHolder, Container, Nameable{
	use ContainerTrait;

	/** @var FurnaceInventory */
	protected $inventory;

	public function __construct(Level $level, CompoundTag $nbt){
		if($nbt->getShort("BurnTime", -1) < 0){
			$nbt->setShort("BurnTime", 0);
		}
		if($nbt->getShort("CookTime", -1) < 0 or ($nbt->getShort("BurnTime") === 0 and $nbt->getShort("CookTime") > 0)){
			$nbt->setShort("CookTime", 0);
		}
		if(!$nbt->hasTag("MaxTime", ShortTag::class)){
			$nbt->setShort("BurnTime", $nbt->getShort("BurnTime"));
			$nbt->setShort("BurnTicks", 0);
		}

		parent::__construct($level, $nbt);
		$this->inventory = new FurnaceInventory($this);

		$this->initItems($nbt);

		if($this->namedtag->getShort("BurnTime") > 0){
			$this->scheduleUpdate();
		}
	}

	public function getName() : string{
		return $this->hasName() ? $this->namedtag->getString("CustomName") : "Furnace";
	}

	public function hasName() : bool{
		return $this->namedtag->hasTag("CustomName", StringTag::class);
	}

	public function setName(string $str){
		if($str === ""){
			$this->namedtag->removeTag("CustomName");
			return;
		}

		$this->namedtag->setString("CustomName", $str);
	}

	public function close(){
		if($this->closed === false){
			foreach($this->getInventory()->getViewers() as $player){
				$player->removeWindow($this->getInventory());
			}

			$this->inventory = null;

			parent::close();
		}
	}

	/**
	 * @return int
	 */
	public function getSize() : int{
		return 3;
	}

	/**
	 * @return FurnaceInventory
	 */
	public function getInventory(){
		return $this->inventory;
	}

	protected function checkFuel(Item $fuel){
		$ev = new FurnaceBurnEvent($this, $fuel, (int) $fuel->getFuelTime());
		$ev->call();

		if($ev->isCancelled()){
			return;
		}

		$this->namedtag->setShort("MaxTime", $ev->getBurnTime());
		$this->namedtag->setShort("BurnTime", $ev->getBurnTime());
		$this->namedtag->setShort("BurnTicks", 0);
		if($this->getBlock()->getId() === Item::FURNACE){
			$this->getLevel()->setBlock($this, Block::get(Block::BURNING_FURNACE, $this->getBlock()->getDamage()), true);
		}

		if($this->namedtag->getShort("BurnTime") > 0 and $ev->isBurning()){
			$fuel->setCount($fuel->getCount() - 1);
			if($fuel->getCount() === 0){
				$fuel = Item::get(Item::AIR, 0, 0);
			}
			$this->inventory->setFuel($fuel);
		}
	}

	public function onUpdate() : bool{
		if($this->closed === true){
			return false;
		}

		$this->timings->startTiming();

		$ret = false;

		$fuel = $this->inventory->getFuel();
		$raw = $this->inventory->getSmelting();
		$product = $this->inventory->getResult();
		$smelt = $this->server->getCraftingManager()->matchFurnaceRecipe($raw);
		$canSmelt = ($smelt instanceof FurnaceRecipe and $raw->getCount() > 0 and (($smelt->getResult()->equals($product) and $product->getCount() < $product->getMaxStackSize()) or $product->getId() === Item::AIR));

		if($this->namedtag->getShort("BurnTime") <= 0 and $canSmelt and $fuel->getFuelTime() !== null and $fuel->getCount() > 0){
			$this->checkFuel($fuel);
		}

		if($this->namedtag->getShort("BurnTime") > 0){
			$this->namedtag->setShort("BurnTime", $this->namedtag->getShort("BurnTime") - 1);
			$this->namedtag->setShort("BurnTicks", (int) ceil($this->namedtag->getShort("BurnTime") / $this->namedtag->getShort("MaxTime") * 200));

			if($smelt instanceof FurnaceRecipe and $canSmelt){
				$this->namedtag->setShort("CookTime", $this->namedtag->getShort("CookTime") + 1);
				if($this->namedtag->getShort("CookTime") >= 200){ //10 seconds
					$product = Item::get($smelt->getResult()->getId(), $smelt->getResult()->getDamage(), $product->getCount() + 1);

					$ev = new FurnaceSmeltEvent($this, $raw, $product);
					$ev->call();

					if(!$ev->isCancelled()){
						$this->inventory->setResult($ev->getResult());
						$raw->setCount($raw->getCount() - 1);
						if($raw->getCount() === 0){
							$raw = Item::get(Item::AIR, 0, 0);
						}
						$this->inventory->setSmelting($raw);
					}

					$this->namedtag->setShort("CookTime", $this->namedtag->getShort("CookTime") - 200);
				}
			}elseif($this->namedtag->getShort("BurnTime") <= 0){
				$this->namedtag->setShort("BurnTime", 0);
				$this->namedtag->setShort("CookTime", 0);
				$this->namedtag->setShort("BurnTicks", 0);
			}else{
				$this->namedtag->setShort("CookTime", 0);
			}
			$ret = true;
		}else{
			if($this->getBlock()->getId() === Item::BURNING_FURNACE){
				$this->getLevel()->setBlock($this, Block::get(Block::FURNACE, $this->getBlock()->getDamage()), true);
			}
			$this->namedtag->setShort("BurnTime", 0);
			$this->namedtag->setShort("CookTime", 0);
			$this->namedtag->setShort("BurnTicks", 0);
		}

		foreach($this->getInventory()->getViewers() as $player){
			$windowId = $player->getWindowId($this->getInventory());
			if($windowId > 0){
				$pk = new ContainerSetDataPacket();
				$pk->windowId = $windowId;
				$pk->property = 0; //Smelting
				$pk->value = $this->namedtag->getShort("CookTime");
				$player->sendDataPacket($pk);

				$pk = new ContainerSetDataPacket();
				$pk->windowId = $windowId;
				$pk->property = 1; //Fire icon
				$pk->value = $this->namedtag->getShort("BurnTicks");
				$player->sendDataPacket($pk);
			}

		}

		$this->lastUpdate = microtime(true);

		$this->timings->stopTiming();

		return $ret;
	}

	public function addAdditionalSpawnData(CompoundTag $nbt, bool $isBedrock){
		$nbt->setShort("BurnTime", $this->namedtag->getShort("BurnTime"));
		$nbt->setShort("CookTime", $this->namedtag->getShort("CookTime"));

		if($this->hasName()){
			$nbt->setString("CustomName", $this->namedtag->getString("CustomName"));
		}
	}
}
