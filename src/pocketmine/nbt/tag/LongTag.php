<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;
use pocketmine\nbt\NbtStreamReader;
use pocketmine\nbt\NbtStreamWriter;

final class LongTag extends ImmutableTag{
	use IntegerishTagTrait;

	protected function min() : int{
		return -0x7fffffffffffffff - 1; //workaround parser bug https://bugs.php.net/bug.php?id=53934
	}

	protected function max() : int{ return 0x7fffffffffffffff; }

	protected function getTypeName() : string{
		return "Long";
	}

	public function getType() : int{
		return NBT::TAG_Long;
	}

	public static function read(NbtStreamReader $reader) : self{
		return new self($reader->readLong());
	}

	public function write(NbtStreamWriter $writer) : void{
		$writer->writeLong($this->value);
	}
}
