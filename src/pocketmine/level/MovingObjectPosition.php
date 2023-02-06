<?php

declare(strict_types=1);

namespace pocketmine\level;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\math\RayTraceResult;

class MovingObjectPosition{
	public const TYPE_BLOCK_COLLISION = 0;
	public const TYPE_ENTITY_COLLISION = 1;

	/** @var RayTraceResult */
	public $hitResult;

	/** @var int */
	public $typeOfHit;

	/** @var Entity|null */
	public $entityHit = null;
	/** @var Block|null */
	public $blockHit = null;

	protected function __construct(int $hitType, RayTraceResult $hitResult){
		$this->typeOfHit = $hitType;
		$this->hitResult = $hitResult;
	}

	/**
	 * @param Block          $block
	 * @param RayTraceResult $result
	 *
	 * @return MovingObjectPosition
	 */
	public static function fromBlock(Block $block, RayTraceResult $result) : MovingObjectPosition{
		$ob = new MovingObjectPosition(self::TYPE_BLOCK_COLLISION, $result);
		$ob->blockHit = $block;
		return $ob;
	}

	/**
	 * @param Entity         $entity
	 *
	 * @param RayTraceResult $result
	 *
	 * @return MovingObjectPosition
	 */
	public static function fromEntity(Entity $entity, RayTraceResult $result) : MovingObjectPosition{
		$ob = new MovingObjectPosition(self::TYPE_ENTITY_COLLISION, $result);
		$ob->entityHit = $entity;

		return $ob;
	}
}