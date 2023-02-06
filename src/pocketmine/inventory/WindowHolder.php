<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\math\Vector3;

class WindowHolder extends Vector3 implements InventoryHolder{
	/** @var WindowInventory */
	protected $inventory;

	public function __construct(WindowInventory $inventory){
		parent::__construct();

		$this->inventory = $inventory;
	}

	/**
	 * @return Inventory
	 */
	public function getInventory() : Inventory{
		return $this->inventory;
	}
}