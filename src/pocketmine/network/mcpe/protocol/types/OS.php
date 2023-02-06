<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types;

final class OS{

	public const ANDROID = 1;
	public const IOS = 2;
	public const MACOS = 3;
	public const FIRE_OS = 4;
	public const GEAR_VR = 5;
	public const HOLOLENS = 6;
	public const WINDOWS_10 = 7;
	public const WINDOWS_MOBILE = 8;
	public const DEDICATED = 9;
	public const TVOS = 10;

	public const UNKNOWN = -1;

	private function __construct(){
		// oof
	}
}
