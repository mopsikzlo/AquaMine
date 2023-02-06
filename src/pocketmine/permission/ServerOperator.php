<?php

declare(strict_types=1);

namespace pocketmine\permission;


interface ServerOperator{
	/**
	 * Checks if the current object has operator permissions
	 *
	 * @return bool
	 */
	public function isOp() : bool;

	/**
	 * Sets the operator permission for the current object
	 *
	 * @param bool $value
	 */
	public function setOp(bool $value);
}