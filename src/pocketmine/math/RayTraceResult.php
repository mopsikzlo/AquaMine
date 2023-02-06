<?php

declare(strict_types=1);

namespace pocketmine\math;

/**
 * Class representing a ray trace collision with an AxisAlignedBB
 */
class RayTraceResult{

	/**
	 * @var AxisAlignedBB
	 */
	public $bb;
	/**
	 * @var int
	 */
	public $hitFace;
	/**
	 * @var Vector3
	 */
	public $hitVector;

	/**
	 * @param AxisAlignedBB $bb
	 * @param int           $hitFace one of the Vector3::SIDE_* constants
	 * @param Vector3       $hitVector
	 */
	public function __construct(AxisAlignedBB $bb, int $hitFace, Vector3 $hitVector){
		$this->bb = $bb;
		$this->hitFace = $hitFace;
		$this->hitVector = $hitVector;
	}

	/**
	 * @return AxisAlignedBB
	 */
	public function getBoundingBox() : AxisAlignedBB{
		return $this->bb;
	}

	/**
	 * @return int
	 */
	public function getHitFace() : int{
		return $this->hitFace;
	}

	/**
	 * @return Vector3
	 */
	public function getHitVector() : Vector3{
		return $this->hitVector;
	}
}