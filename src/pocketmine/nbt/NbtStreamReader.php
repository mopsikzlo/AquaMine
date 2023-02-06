<?php

declare(strict_types=1);

namespace pocketmine\nbt;

use pocketmine\utils\BinaryDataException;

/**
 * @internal
 */
interface NbtStreamReader{

	/**
	 * @throws BinaryDataException
	 */
	public function readByte() : int;

	/**
	 * @throws BinaryDataException
	 */
	public function readSignedByte() : int;

	/**
	 * @throws BinaryDataException
	 */
	public function readShort() : int;

	/**
	 * @throws BinaryDataException
	 */
	public function readSignedShort() : int;

	/**
	 * @throws BinaryDataException
	 */
	public function readInt() : int;

	/**
	 * @throws BinaryDataException
	 */
	public function readLong() : int;

	/**
	 * @throws BinaryDataException
	 */
	public function readFloat() : float;

	/**
	 * @throws BinaryDataException
	 */
	public function readDouble() : float;

	/**
	 * @throws BinaryDataException
	 */
	public function readByteArray() : string;

	/**
	 * @throws BinaryDataException
	 */
	public function readString() : string;

	/**
	 * @return int[]
	 * @throws BinaryDataException
	 */
	public function readIntArray() : array;
}
