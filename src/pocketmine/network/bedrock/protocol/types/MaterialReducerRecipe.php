<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

final class MaterialReducerRecipe{

    public $inputItemId;
    public $inputItemMeta;
	/**
	 * @var MaterialReducerRecipeOutput[]
	 */
    public $outputs;

	/**
	 * @param MaterialReducerRecipeOutput[] $outputs
	 */
	public function __construct(int $inputItemId, int $inputItemMeta, array $outputs){
		$this->inputItemId = $inputItemId;
		$this->inputItemMeta = $inputItemMeta;
		$this->outputs = $outputs;
	}

	public function getInputItemId() : int{ return $this->inputItemId; }

	public function getInputItemMeta() : int{ return $this->inputItemMeta; }

	/** @return MaterialReducerRecipeOutput[] */
	public function getOutputs() : array{ return $this->outputs; }
}
