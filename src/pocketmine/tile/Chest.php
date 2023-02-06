<?php

declare(strict_types=1);

namespace pocketmine\tile;

use pocketmine\inventory\ChestInventory;
use pocketmine\inventory\DoubleChestInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

class Chest extends Spawnable implements InventoryHolder, Container, Nameable{
	use ContainerTrait;

	/** @var ChestInventory */
	protected $inventory;
	/** @var DoubleChestInventory */
	protected $doubleInventory = null;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->inventory = new ChestInventory($this);

		$this->initItems($nbt);
	}

	public function close(){
		if($this->closed === false){
			foreach($this->getInventory()->getViewers() as $player){
				$player->removeWindow($this->getInventory());
			}

			foreach($this->getInventory()->getViewers() as $player){
				$player->removeWindow($this->getRealInventory());
			}

			$this->inventory = null;
			$this->doubleInventory = null;

			parent::close();
		}
	}

	/**
	 * @return int
	 */
	public function getSize() : int{
		return 27;
	}

	/**
	 * @return ChestInventory|DoubleChestInventory
	 */
	public function getInventory(){
		if($this->isPaired() and $this->doubleInventory === null){
			$this->checkPairing();
		}
		return $this->doubleInventory instanceof DoubleChestInventory ? $this->doubleInventory : $this->inventory;
	}

	/**
	 * @return ChestInventory
	 */
	public function getRealInventory(){
		return $this->inventory;
	}

	protected function checkPairing(){
		if($this->isPaired() and !$this->getLevel()->isChunkLoaded($this->namedtag->getInt("pairx") >> 4, $this->namedtag->getInt("pairz") >> 4)){
			//paired to a tile in an unloaded chunk
			$this->doubleInventory = null;

		}elseif(($pair = $this->getPair()) instanceof Chest){
			if(!$pair->isPaired()){
				$pair->createPair($this);
				$pair->checkPairing();
			}
			if($this->doubleInventory === null){
				if(($pair->x + ($pair->z << 15)) > ($this->x + ($this->z << 15))){ //Order them correctly
					$this->doubleInventory = new DoubleChestInventory($pair, $this);
				}else{
					$this->doubleInventory = new DoubleChestInventory($this, $pair);
				}
			}
		}else{
			$this->doubleInventory = null;
			$this->namedtag->removeTag("pairx", "pairz");
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->namedtag->getString("CustomName", "Chest");
	}

	/**
	 * @return bool
	 */
	public function hasName() : bool{
		return $this->namedtag->hasTag("CustomName", StringTag::class);
	}

	/**
	 * @param string $str
	 */
	public function setName(string $str){
		if($str === ""){
			$this->namedtag->removeTag("CustomName");
			return;
		}

		$this->namedtag->setString("CustomName", $str);
	}

	public function isPaired(){
		if(!$this->namedtag->hasTag("pairx", IntTag::class) or !$this->namedtag->hasTag("pairz", IntTag::class)){
			return false;
		}

		return true;
	}

	/**
	 * @return Chest|null
	 */
	public function getPair(){
		if($this->isPaired()){
			$tile = $this->getLevel()->getTileAt($this->namedtag->getInt("pairx"), $this->y, $this->namedtag->getInt("pairz"));
			if($tile instanceof Chest){
				return $tile;
			}
		}

		return null;
	}

	public function pairWith(Chest $tile){
		if($this->isPaired() or $tile->isPaired()){
			return false;
		}

		$this->createPair($tile);

		$this->spawnToAll();
		$tile->spawnToAll();
		$this->checkPairing();

		return true;
	}

	private function createPair(Chest $tile){
		$this->namedtag->setInt("pairx", $tile->x);
		$this->namedtag->setInt("pairz", $tile->z);

		$tile->namedtag->setInt("pairx", $this->x);
		$tile->namedtag->setInt("pairz", $this->z);
	}

	public function unpair(){
		if(!$this->isPaired()){
			return false;
		}

		$tile = $this->getPair();
		$this->namedtag->removeTag("pairx", "pairz");

		$this->spawnToAll();

		if($tile instanceof Chest){
			$tile->namedtag->removeTag("pairx", "pairz");
			$tile->checkPairing();
			$tile->spawnToAll();
		}
		$this->checkPairing();

		return true;
	}

	public function addAdditionalSpawnData(CompoundTag $nbt, bool $isBedrock){
		if($this->isPaired()){
			$nbt->setInt("pairx", $this->namedtag->getInt("pairx"));
			$nbt->setInt("pairz", $this->namedtag->getInt("pairz"));
		}

		if($this->hasName()){
			$nbt->setString("CustomName", $this->namedtag->getString("CustomName"));
		}
	}
}
