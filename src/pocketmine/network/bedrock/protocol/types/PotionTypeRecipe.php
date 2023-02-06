<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

class PotionTypeRecipe{
	/** @var int */
	private $inputPotionId;
	/** @var int */
	private $inputPotionMeta;
	/** @var int */
	private $ingredientItemId;
	/** @var int */
	private $ingredientItemMeta;
	/** @var int */
	private $outputPotionId;
	/** @var int */
	private $outputPotionMeta;

	public function __construct(int $inputPotionId, int $inputPotionMeta, int $ingredientItemId, int $ingredientItemMeta, int $outputPotionId, int $outputPotionMeta){
		$this->inputPotionId = $inputPotionId;
		$this->inputPotionMeta = $inputPotionMeta;
		$this->ingredientItemId = $ingredientItemId;
		$this->ingredientItemMeta = $ingredientItemMeta;
		$this->outputPotionId = $outputPotionId;
		$this->outputPotionMeta = $outputPotionMeta;
	}

	/**
	 * @return int
	 */
	public function getInputPotionId() : int{
		return $this->inputPotionId;
	}

	/**
	 * @return int
	 */
	public function getInputPotionMeta() : int{
		return $this->inputPotionMeta;
	}

	/**
	 * @return int
	 */
	public function getIngredientItemId() : int{
		return $this->ingredientItemId;
	}

	/**
	 * @return int
	 */
	public function getIngredientItemMeta() : int{
		return $this->ingredientItemMeta;
	}

	/**
	 * @return int
	 */
	public function getOutputPotionId() : int{
		return $this->outputPotionId;
	}

	/**
	 * @return int
	 */
	public function getOutputPotionMeta() : int{
		return $this->outputPotionMeta;
	}
}