<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types;

class Experiments{

	/** @var bool[] */
	public $experiments;
	/** @var bool */
	public $hasPreviouslyUsedExperiments;

	public function __construct(array $experiments, bool $hasPreviouslyUsedExperiments){
		$this->experiments = $experiments;
		$this->hasPreviouslyUsedExperiments = $hasPreviouslyUsedExperiments;
	}
}