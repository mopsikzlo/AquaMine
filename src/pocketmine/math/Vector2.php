<?php

declare(strict_types=1);

namespace pocketmine\math;

use function abs;
use function round;
use function sqrt;

class Vector2{
	public $x;
	public $y;

	public function __construct($x = 0, $y = 0){
		$this->x = $x;
		$this->y = $y;
	}

	public function getX(){
		return $this->x;
	}

	public function getY(){
		return $this->y;
	}

	public function getFloorX(){
		return (int) $this->x;
	}

	public function getFloorY(){
		return (int) $this->y;
	}

	public function add($x, $y = 0){
		if($x instanceof Vector2){
			return $this->add($x->x, $x->y);
		}else{
			return new Vector2($this->x + $x, $this->y + $y);
		}
	}

	public function subtract($x, $y = 0){
		if($x instanceof Vector2){
			return $this->add(-$x->x, -$x->y);
		}else{
			return $this->add(-$x, -$y);
		}
	}

	public function ceil(){
		return new Vector2((int) ($this->x + 1), (int) ($this->y + 1));
	}

	public function floor(){
		return new Vector2((int) $this->x, (int) $this->y);
	}

	public function round(){
		return new Vector2(round($this->x), round($this->y));
	}

	public function abs(){
		return new Vector2(abs($this->x), abs($this->y));
	}

	public function multiply($number){
		return new Vector2($this->x * $number, $this->y * $number);
	}

	public function divide($number){
		return new Vector2($this->x / $number, $this->y / $number);
	}

	public function distance($x, $y = 0){
		if($x instanceof Vector2){
			return sqrt($this->distanceSquared($x->x, $x->y));
		}else{
			return sqrt($this->distanceSquared($x, $y));
		}
	}

	public function distanceSquared($x, $y = 0){
		if($x instanceof Vector2){
			return $this->distanceSquared($x->x, $x->y);
		}else{
			return (($this->x - $x) ** 2) + (($this->y - $y) ** 2);
		}
	}

	public function length(){
		return sqrt($this->lengthSquared());
	}

	public function lengthSquared(){
		return $this->x * $this->x + $this->y * $this->y;
	}

	public function normalize(){
		$len = $this->lengthSquared();
		if($len != 0){
			return $this->divide(sqrt($len));
		}

		return new Vector2(0, 0);
	}

	public function dot(Vector2 $v){
		return $this->x * $v->x + $this->y * $v->y;
	}

	public function __toString(){
		return "Vector2(x=" . $this->x . ",y=" . $this->y . ")";
	}

}