<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;
use pocketmine\nbt\NbtStreamReader;
use pocketmine\nbt\NbtStreamWriter;

final class ByteTag extends ImmutableTag{
	use IntegerishTagTrait;

	protected function min() : int{ return -0x80; }

	protected function max() : int{ return 0x7f; }

	protected function getTypeName() : string{
		return "Byte";
	}

	public function getType() : int{
		return NBT::TAG_Byte;
	}

	public static function read(NbtStreamReader $reader) : self{
		return new self($reader->readSignedByte());
	}

	public function write(NbtStreamWriter $writer) : void{
		$writer->writeByte($this->value);
	}
}
