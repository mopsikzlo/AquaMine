<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

final class InputMode{

	public const MOUSE_KEYBOARD = 1;
	public const TOUCHSCREEN = 2;
	public const GAMEPAD = 3;
	public const MOTION_CONTROLLER = 4;

	private function __construct(){
		// oof
	}
}