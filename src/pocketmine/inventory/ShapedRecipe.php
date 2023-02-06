<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;
use pocketmine\math\Vector2;
use pocketmine\Server;
use pocketmine\utils\UUID;

use function array_fill;
use function array_key_exists;
use function count;

class ShapedRecipe implements Recipe{
	/** @var Item */
	private $output;

	/** @var UUID|null */
	private $id = null;

	/** @var string[] */
	private $shape = [];

	/** @var Item[][] */
	private $ingredients = [];
	/** @var Vector2[][] */
	private $shapeItems = [];

	/**
	 * @param Item $result
	 * @param int  $height
	 * @param int  $width
	 *
	 * @throws \Exception
	 */
	public function __construct(Item $result, int $height, int $width){
		for($h = 0; $h < $height; $h++){
			if($width === 0 or $width > 3){
				throw new \InvalidStateException("Crafting rows should be 1, 2, 3 wide, not $width");
			}
			$this->ingredients[] = array_fill(0, $width, null);
		}

		$this->output = clone $result;
	}

	public function getWidth() : int{
		return count($this->ingredients[0]);
	}

	public function getHeight() : int{
		return count($this->ingredients);
	}

	/**
	 * @return Item
	 */
	public function getResult() : Item{
		return $this->output;
	}

	/**
	 * @return UUID|null
	 */
	public function getId(){
		return $this->id;
	}

	public function setId(UUID $id){
		if($this->id !== null){
			throw new \InvalidStateException("Id is already set");
		}

		$this->id = $id;
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param Item $item
	 *
	 * @return $this
	 */
	public function addIngredient(int $x, int $y, Item $item){
		$this->ingredients[$y][$x] = clone $item;
		return $this;
	}

	/**
	 * @param string $key
	 * @param Item   $item
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function setIngredient(string $key, Item $item){
		if(!array_key_exists($key, $this->shape)){
			throw new \Exception("Symbol does not appear in the shape: " . $key);
		}

		$item->setCount(1);
		$this->fixRecipe($key, $item);

		return $this;
	}

	/**
	 * @param string $key
	 * @param Item $item
	 */
	protected function fixRecipe(string $key, Item $item){
		foreach($this->shapeItems[$key] as $entry){
			$this->ingredients[$entry->y][$entry->x] = clone $item;
		}
	}

	/**
	 * @return Item[][]
	 */
	public function getIngredientMap() : array{
		$ingredients = [];
		foreach($this->ingredients as $y => $row){
			$ingredients[$y] = [];
			foreach($row as $x => $ingredient){
				if($ingredient !== null){
					$ingredients[$y][$x] = clone $ingredient;
				}else{
					$ingredients[$y][$x] = Item::get(Item::AIR);
				}
			}
		}

		return $ingredients;
	}

	/**
	 * @param int $x
	 * @param int $y
	 *
	 * @return Item
	 */
	public function getIngredient(int $x, int $y) : Item{
		return $this->ingredients[$y][$x] ?? Item::get(Item::AIR);
	}

	 /**
 	 * @return Item[]
 	 */
 	public function getIngredientList(){
 		$ingredients = [];
 		for ($x = 0; $x < 3; ++$x){
 			for ($y = 0; $y < 3; ++$y){
 				if (!empty($this->ingredients[$x][$y])){
 					if ($this->ingredients[$x][$y]->getId() !== Item::AIR){
 						$ingredients[] = clone $this->ingredients[$x][$y];
 					}
 				}
 			}
 		}
 		return $ingredients;
 	}

	/**
	 * @return string[]
	 */
	public function getShape() : array{
		return $this->shape;
	}

	public function registerToCraftingManager(){
		Server::getInstance()->getCraftingManager()->registerShapedRecipe($this);
	}
}
