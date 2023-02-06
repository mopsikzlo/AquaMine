<?php

declare(strict_types=1);

namespace pocketmine\nbt\tag;

use RuntimeException;
use function get_class;

trait NoDynamicFieldsTrait{

	private function throw(string $field) : RuntimeException{
		return new RuntimeException("Cannot access dynamic field \"$field\": Dynamic field access on " . get_class($this) . " is no longer supported");
	}

	/**
	 * @param string $name
	 *
	 * @phpstan-return never
	 */
	public function __get(string $name){
		throw $this->throw($name);
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 *
	 * @phpstan-return never
	 */
	public function __set(string $name, $value){
		throw $this->throw($name);
	}

	/**
	 * @param string $name
	 *
	 * @phpstan-return never
	 */
	public function __isset(string $name){
		throw $this->throw($name);
	}

	/**
	 * @param string $name
	 *
	 * @phpstan-return never
	 */
	public function __unset(string $name){
		throw $this->throw($name);
	}
}
