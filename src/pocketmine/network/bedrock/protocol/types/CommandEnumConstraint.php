<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

class CommandEnumConstraint{
	/** @var CommandEnum */
	private $enum;
	/** @var int */
	private $valueOffset;
	/** @var int[] */
	private $constraints; //TODO: find constants

	/**
	 * @param CommandEnum $enum
	 * @param int         $valueOffset
	 * @param int[]       $constraints
	 */
	public function __construct(CommandEnum $enum, int $valueOffset, array $constraints){
		(static function(int ...$_){})(...$constraints);
		if(!isset($enum->enumValues[$valueOffset])){
			throw new \InvalidArgumentException("Invalid enum value offset $valueOffset");
		}
		$this->enum = $enum;
		$this->valueOffset = $valueOffset;
		$this->constraints = $constraints;
	}

	public function getEnum() : CommandEnum{
		return $this->enum;
	}

	public function getValueOffset() : int{
		return $this->valueOffset;
	}

	public function getAffectedValue() : string{
		return $this->enum->enumValues[$this->valueOffset];
	}

	/**
	 * @return int[]
	 */
	public function getConstraints() : array{
		return $this->constraints;
	}
}