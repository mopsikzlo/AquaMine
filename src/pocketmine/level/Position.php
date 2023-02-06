<?php

declare(strict_types=1);

namespace pocketmine\level;

use pocketmine\math\Vector3;
use pocketmine\utils\MainLogger;

use function assert;

class Position extends Vector3{

	/** @var Level */
	public $level = null;

	/**
	 * @param int   $x
	 * @param int   $y
	 * @param int   $z
	 * @param Level $level
	 */
	public function __construct($x = 0, $y = 0, $z = 0, Level $level = null){
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		$this->level = $level;
	}

	public static function fromObject(Vector3 $pos, Level $level = null){
		return new Position($pos->x, $pos->y, $pos->z, $level);
	}

	/**
	 * Return a Position instance
	 *
	 * @return Position
	 */
	public function asPosition() : Position{
		return new Position($this->x, $this->y, $this->z, $this->level);
	}

	/**
	 * Returns the target Level, or null if the target is not valid.
	 * If a reference exists to a Level which is closed, the reference will be destroyed and null will be returned.
	 *
	 * @return Level|null
	 */
	public function getLevel(){
		if($this->level !== null and $this->level->isClosed()){
			MainLogger::getLogger()->debug("Position was holding a reference to an unloaded Level");
			$this->level = null;
		}

		return $this->level;
	}

	/**
	 * Sets the target Level of the position.
	 *
	 * @param Level|null $level
	 *
	 * @return $this
	 *
	 * @throws \InvalidArgumentException if the specified Level has been closed
	 */
	public function setLevel(Level $level = null){
		if($level !== null and $level->isClosed()){
			throw new \InvalidArgumentException("Specified level has been unloaded and cannot be used");
		}

		$this->level = $level;
		return $this;
	}

	/**
	 * Checks if this object has a valid reference to a loaded Level
	 *
	 * @return bool
	 */
	public function isValid() : bool{
		return $this->getLevel() instanceof Level;
	}

	/**
	 * Returns a side Vector
	 *
	 * @param int $side
	 * @param int $step
	 *
	 * @return Position
	 *
	 * @throws LevelException
	 */
	public function getSide($side, $step = 1){
		assert($this->isValid());

		return Position::fromObject(parent::getSide($side, $step), $this->level);
	}

	public function __toString(){
		return "Position(level=" . ($this->isValid() ? $this->getLevel()->getName() : "null") . ",x=" . $this->x . ",y=" . $this->y . ",z=" . $this->z . ")";
	}

	/**
	 * @param $x
	 * @param $y
	 * @param $z
	 *
	 * @return Position
	 */
	public function setComponents($x, $y, $z){
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		return $this;
	}

	public function equals(Vector3 $v) : bool{
		if($v instanceof Position){
			return parent::equals($v) and $v->getLevel() === $this->getLevel();
		}
		return parent::equals($v);
	}
}
