<?php

declare(strict_types=1);

namespace pocketmine\event\inventory;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\inventory\Recipe;
use pocketmine\item\Item;
use pocketmine\Player;

use function array_map;

class CraftItemEvent extends Event implements Cancellable{
	public static $handlerList = null;

	/** @var Item[] */
	private $input;
	/** @var Recipe */
	private $recipe;
	/** @var Player */
	private $player;


	/**
	 * @param Player $player
	 * @param Item[] $input
	 * @param Recipe $recipe
	 */
	public function __construct(Player $player, array $input, Recipe $recipe){
		$this->player = $player;
		$this->input = $input;
		$this->recipe = $recipe;
	}

	/**
	 * @return Item[]
	 */
	public function getInput() : array{
		return array_map(function(Item $item) : Item{
			return clone $item;
		}, $this->input);
	}

	/**
	 * @return Recipe
	 */
	public function getRecipe() : Recipe{
		return $this->recipe;
	}

	/**
	 * @return Player
	 */
	public function getPlayer() : Player{
		return $this->player;
	}
}
