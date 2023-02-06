<?php

declare(strict_types=1);

namespace pocketmine\math;


use function cos;
use function sin;

abstract class VectorMath{

	public static function getDirection2D($azimuth){
		return new Vector2(cos($azimuth), sin($azimuth));
	}

	public static function getDirection3D($azimuth, $inclination) : Vector3{
		$yFact = cos($inclination);
		return new Vector3($yFact * cos($azimuth), sin($inclination), $yFact * sin($azimuth));
	}

}