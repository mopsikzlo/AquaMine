<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\utils\UUID;

use function count;

class ShapelessRecipe implements Recipe{
	/** @var Item */
	private $output;

	/** @var UUID|null */
	private $id = null;

	/** @var Item[] */
	private $ingredients = [];

	public function __construct(Item $result){
		$this->output = clone $result;
	}

	/**
	 * @return UUID|null
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * @param UUID $id
	 */
	public function setId(UUID $id){
		if($this->id !== null){
			throw new \InvalidStateException("Id is already set");
		}

		$this->id = $id;
	}

	public function getResult() : Item{
		return clone $this->output;
	}

	/**
	 * @param Item $item
	 *
	 * @return ShapelessRecipe
	 *
	 * @throws \InvalidArgumentException
	 */
	public function addIngredient(Item $item) : ShapelessRecipe{
		if(count($this->ingredients) >= 9){
			throw new \InvalidArgumentException("Shapeless recipes cannot have more than 9 ingredients");
		}

		$it = clone $item;
		$it->setCount(1);

		while($item->getCount() > 0){
			$this->ingredients[] = clone $it;
			$item->setCount($item->getCount() - 1);
		}

		return $this;
	}

	/**
	 * @param Item $item
	 *
	 * @return $this
	 */
	public function removeIngredient(Item $item){
		foreach($this->ingredients as $index => $ingredient){
			if($item->getCount() <= 0){
				break;
			}
			if($ingredient->equals($item, !$item->hasAnyDamageValue(), $item->hasCompoundTag())){
				unset($this->ingredients[$index]);
				$item->setCount($item->getCount() - 1);
			}
		}

		return $this;
	}

	/**
	 * @return Item[]
	 */
	public function getIngredientList() : array{
		$ingredients = [];
		foreach($this->ingredients as $ingredient){
			$ingredients[] = clone $ingredient;
		}

		return $ingredients;
	}

	/**
	 * @return int
	 */
	public function getIngredientCount() : int{
		$count = 0;
		foreach($this->ingredients as $ingredient){
			$count += $ingredient->getCount();
		}

		return $count;
	}

	public function registerToCraftingManager(){
		Server::getInstance()->getCraftingManager()->registerShapelessRecipe($this);
	}
}