<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;
use pocketmine\nbt\NbtStreamReader;
use pocketmine\nbt\NbtStreamWriter;

final class IntTag extends ImmutableTag{
	use IntegerishTagTrait;

	protected function min() : int{
		return -0x7fffffff - 1; //workaround parser bug https://bugs.php.net/bug.php?id=53934
	}

	protected function max() : int{ return 0x7fffffff; }

	protected function getTypeName() : string{
		return "Int";
	}

	public function getType() : int{
		return NBT::TAG_Int;
	}

	public static function read(NbtStreamReader $reader) : self{
		return new self($reader->readInt());
	}

	public function write(NbtStreamWriter $writer) : void{
		$writer->writeInt($this->value);
	}
}
