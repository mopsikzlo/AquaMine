<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;
use pocketmine\nbt\NbtStreamReader;
use pocketmine\nbt\NbtStreamWriter;

final class ShortTag extends ImmutableTag{
	use IntegerishTagTrait;

	protected function min() : int{ return -0x8000; }

	protected function max() : int{ return 0x7fff; }

	protected function getTypeName() : string{
		return "Short";
	}

	public function getType() : int{
		return NBT::TAG_Short;
	}

	public static function read(NbtStreamReader $reader) : self{
		return new self($reader->readSignedShort());
	}

	public function write(NbtStreamWriter $writer) : void{
		$writer->writeShort($this->value);
	}
}
