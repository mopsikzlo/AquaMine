<?php

declare(strict_types=1);

namespace pocketmine\item;

class Dye extends Item{

	public const BLACK = 0, INK_SAC = 0;
	public const RED = 1;
	public const GREEN = 2;
	public const BROWN = 3, COCOA_BEANS = 3;
	public const BLUE = 4, LAPIS_LAZULI = 4;
	public const PURPLE = 5;
	public const CYAN = 6;
	public const LIGHT_GRAY = 7;
	public const GRAY = 8;
	public const PINK = 9;
	public const LIME = 10;
	public const YELLOW = 11;
	public const LIGHT_BLUE = 12;
	public const MAGENTA = 13;
	public const ORANGE = 14;
	public const WHITE = 15, BONE_MEAL = 15;

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::DYE, $meta, $count, "Dye");
	}

}

