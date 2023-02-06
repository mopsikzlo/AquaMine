<?php

declare(strict_types=1);

namespace pocketmine\nbt;

use function array_values;
use function count;
use function pack;
use function unpack;

class LittleEndianNbtSerializer extends BaseNbtSerializer{

	public function readShort() : int{
		return $this->buffer->getLShort();
	}

	public function readSignedShort() : int{
		return $this->buffer->getSignedLShort();
	}

	public function writeShort(int $v) : void{
		$this->buffer->putLShort($v);
	}

	public function readInt() : int{
		return $this->buffer->getLInt();
	}

	public function writeInt(int $v) : void{
		$this->buffer->putLInt($v);
	}

	public function readLong() : int{
		return $this->buffer->getLLong();
	}

	public function writeLong(int $v) : void{
		$this->buffer->putLLong($v);
	}

	public function readFloat() : float{
		return $this->buffer->getLFloat();
	}

	public function writeFloat(float $v) : void{
		$this->buffer->putLFloat($v);
	}

	public function readDouble() : float{
		return $this->buffer->getLDouble();
	}

	public function writeDouble(float $v) : void{
		$this->buffer->putLDouble($v);
	}

	public function readIntArray() : array{
		$len = $this->readInt();
		return array_values(unpack("V*", $this->buffer->get($len * 4)));
	}

	public function writeIntArray(array $array) : void{
		$this->writeInt(count($array));
		$this->buffer->put(pack("V*", ...$array));
	}
}
