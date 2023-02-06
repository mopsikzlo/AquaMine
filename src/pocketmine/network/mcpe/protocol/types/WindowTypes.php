<?php

declare(strict_types=1);


namespace pocketmine\network\mcpe\protocol\types;


interface WindowTypes{

	public const INVENTORY = -1;
	public const CONTAINER = 0;
	public const WORKBENCH = 1;
	public const FURNACE = 2;
	public const ENCHANTMENT = 3;
	public const BREWING_STAND = 4;
	public const ANVIL = 5;
	public const DISPENSER = 6;
	public const DROPPER = 7;
	public const HOPPER = 8;
	public const CAULDRON = 9;
	public const MINECART_CHEST = 10;
	public const MINECART_HOPPER = 11;
	public const HORSE = 12;
	public const BEACON = 13;
	public const STRUCTURE_EDITOR = 14;
	public const TRADING = 15;
	public const COMMAND_BLOCK = 16;

}