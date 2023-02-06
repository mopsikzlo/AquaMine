<?php

declare(strict_types=1);

namespace pocketmine\tile;

interface Nameable{


	/**
	 * @return string
	 */
	public function getName() : string;

	/**
	 * @param string $str
	 */
	public function setName(string $str);

	/**
	 * @return bool
	 */
	public function hasName() : bool;
}
