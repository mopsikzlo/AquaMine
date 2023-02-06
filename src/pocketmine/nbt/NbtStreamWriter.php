<?php

declare(strict_types=1);

namespace pocketmine\nbt;

use InvalidArgumentException;

/**
 * @internal
 */
interface NbtStreamWriter{

	public function writeByte(int $v) : void;

	public function writeShort(int $v) : void;

	public function writeInt(int $v) : void;

	public function writeLong(int $v) : void;

	public function writeFloat(float $v) : void;

	public function writeDouble(float $v) : void;

	public function writeByteArray(string $v) : void;

	/**
	 * @param string $v
	 *
	 * @throws InvalidArgumentException if the string is too long
	 */
	public function writeString(string $v) : void;

	/**
	 * @param int[] $array
	 */
	public function writeIntArray(array $array) : void;
}
