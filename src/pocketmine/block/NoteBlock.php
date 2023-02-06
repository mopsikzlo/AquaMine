<?php

declare(strict_types=1);

namespace pocketmine\block;

class NoteBlock extends Solid{

	protected $id = self::NOTE_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Note Block";
	}
}
