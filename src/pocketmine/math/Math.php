<?php

declare(strict_types=1);

/**
 * Math related classes, like matrices, bounding boxes and vector
 */
namespace pocketmine\math;


use function sqrt;

abstract class Math{

	/**
	 * @param float $n
	 *
	 * @return int
	 */
	public static function floorFloat($n) : int{
		$i = (int) $n;
		return $n >= $i ? $i : $i - 1;
	}

	/**
	 * @param float $n
	 *
	 * @return int
	 */
	public static function ceilFloat($n) : int{
		$i = (int) $n;
		return $n <= $i ? $i : $i + 1;
	}

	/**
	 * @param int|float $num
	 *
	 * @return int
	 */
	public static function signum($num) : int{
		if($num == 0){
			return 0;
		}
		return $num > 0 ? 1 : -1;
	}

	public static function clamp($value, $low, $high){
		return min($high, max($low, $value));
	}

	/**
	 * Solves a quadratic equation with the given coefficients and returns an array of up to two solutions.
	 *
	 * @param float $a
	 * @param float $b
	 * @param float $c
	 *
	 * @return float[]
	 */
	public static function solveQuadratic(float $a, float $b, float $c) : array{
		$discriminant = $b ** 2 - 4 * $a * $c;
		if($discriminant > 0){ //2 real roots
			$sqrtDiscriminant = sqrt($discriminant);
			return [
				(-$b + $sqrtDiscriminant) / (2 * $a),
				(-$b - $sqrtDiscriminant) / (2 * $a)
			];
		}elseif($discriminant == 0){ //1 real root
			return [
				-$b / (2 * $a)
			];
		}else{ //No real roots
			return [];
		}
	}

	/**
	 * Returns the next pseudorandom, Gaussian ("normally") distributed double
	 * value with mean 0.0 and standard deviation 1.0.
	 *
	 * @return float
	 */
	public static function randomGaussian() : float{
		return sqrt(-2 * log(lcg_value())) * cos(2 * M_PI * lcg_value());
	}
}
