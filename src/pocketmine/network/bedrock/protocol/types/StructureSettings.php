<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

class StructureSettings{

	/** @var string */
	public $paletteName;
	/** @var bool */
	public $ignoreEntities;
	/** @var bool */
	public $ignoreBlocks;
	/** @var int */
	public $structureSizeX;
	/** @var int */
	public $structureSizeY;
	/** @var int */
	public $structureSizeZ;
	/** @var int */
	public $structureOffsetX;
	/** @var int */
	public $structureOffsetY;
	/** @var int */
	public $structureOffsetZ;
	/** @var int */
	public $lastTouchedByPlayerId;
	/** @var int */
	public $rotation;
	/** @var int */
	public $mirror;
	/** @var float */
	public $integrityValue;
	/** @var int */
	public $integritySeed;
}
