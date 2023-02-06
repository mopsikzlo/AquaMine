<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

class EnchantmentOption{

	/** @var int */
	public $cost;
	/** @var ItemEnchantments */
	public $enchantments;
	/** @var string */
	public $name;
	/** @var int */
	public $recipeNetworkId;

	public function __construct(int $cost = -1, ?ItemEnchantments $enchantments = null, string $name = "", int $recipeNetworkId = -1){
		$this->cost = $cost;
		$this->enchantments = $enchantments;
		$this->name = $name;
		$this->recipeNetworkId = $recipeNetworkId;
	}
}