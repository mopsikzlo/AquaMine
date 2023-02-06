<?php

declare(strict_types=1);

namespace pocketmine\block;

class TripwireHook extends Flowable{

	protected $id = self::TRIPWIRE_HOOK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Tripwire Hook";
	}
}
