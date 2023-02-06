<?php

declare(strict_types=1);

namespace pocketmine\nbt;

use function array_values;
use function count;
use function pack;
use function unpack;

class BigEndianNbtSerializer extends BaseNbtSerializer{

	public function readShort() : int{
		return $this->buffer->getShort();
	}

	public function readSignedShort() : int{
		return $this->buffer->getSignedShort();
	}

	public function writeShort(int $v) : void{
		$this->buffer->putShort($v);
	}

	public function readInt() : int{
		return $this->buffer->getInt();
	}

	public function writeInt(int $v) : void{
		$this->buffer->putInt($v);
	}

	public function readLong() : int{
		return $this->buffer->getLong();
	}

	public function writeLong(int $v) : void{
		$this->buffer->putLong($v);
	}

	public function readFloat() : float{
		return $this->buffer->getFloat();
	}

	public function writeFloat(float $v) : void{
		$this->buffer->putFloat($v);
	}

	public function readDouble() : float{
		return $this->buffer->getDouble();
	}

	public function writeDouble(float $v) : void{
		$this->buffer->putDouble($v);
	}

	public function readIntArray() : array{
		$len = $this->readInt();
		return array_values(unpack("N*", $this->buffer->get($len * 4)));
	}

	public function writeIntArray(array $array) : void{
		$this->writeInt(count($array));
		$this->buffer->put(pack("N*", ...$array));
	}
}
