<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;

//TODO: remove this
abstract class Fuel{
	public static $duration = [
		Item::COAL => 1600,
		Item::COAL_BLOCK => 16000,
		Item::WOOD => 300,
		Item::WOODEN_PLANKS => 300,
		Item::SAPLING => 100,
		Item::WOODEN_AXE => 200,
		Item::WOODEN_PICKAXE => 200,
		Item::WOODEN_SWORD => 200,
		Item::WOODEN_SHOVEL => 200,
		Item::WOODEN_HOE => 200,
		Item::STICK => 100,
		Item::FENCE => 300,
		Item::FENCE_GATE => 300,
		Item::SPRUCE_FENCE_GATE => 300,
		Item::BIRCH_FENCE_GATE => 300,
		Item::JUNGLE_FENCE_GATE => 300,
		Item::ACACIA_FENCE_GATE => 300,
		Item::DARK_OAK_FENCE_GATE => 300,
		Item::WOODEN_STAIRS => 300,
		Item::SPRUCE_STAIRS => 300,
		Item::BIRCH_STAIRS => 300,
		Item::JUNGLE_STAIRS => 300,
		Item::TRAPDOOR => 300,
		Item::WORKBENCH => 300,
		Item::BOOKSHELF => 300,
		Item::CHEST => 300,
		Item::BUCKET => 20000,
	];

}