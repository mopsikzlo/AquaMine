<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;
use pocketmine\nbt\NbtStreamReader;
use pocketmine\nbt\NbtStreamWriter;
use function func_num_args;

final class DoubleTag extends ImmutableTag{
	/** @var float */
	private $value;

	public function __construct(float $value){
		self::restrictArgCount(__METHOD__, func_num_args(), 1);
		$this->value = $value;
	}

	protected function getTypeName() : string{
		return "Double";
	}

	public function getType() : int{
		return NBT::TAG_Double;
	}

	public static function read(NbtStreamReader $reader) : self{
		return new self($reader->readDouble());
	}

	public function write(NbtStreamWriter $writer) : void{
		$writer->writeDouble($this->value);
	}

	public function getValue() : float{
		return $this->value;
	}

	protected function stringifyValue(int $indentation) : string{
		return (string) $this->value;
	}
}
