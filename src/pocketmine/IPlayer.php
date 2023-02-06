<?php

declare(strict_types=1);

namespace pocketmine;

use pocketmine\permission\ServerOperator;

interface IPlayer extends ServerOperator{

	/**
	 * @return bool
	 */
	public function isOnline() : bool;

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return bool
	 */
	public function isBanned() : bool;

	/**
	 * @param bool $banned
	 */
	public function setBanned(bool $banned);

	/**
	 * @return bool
	 */
	public function isWhitelisted() : bool;

	/**
	 * @param bool $value
	 */
	public function setWhitelisted(bool $value);

	/**
	 * @return Player|null
	 */
	public function getPlayer();

	/**
	 * @return int|double
	 */
	public function getFirstPlayed();

	/**
	 * @return int|double
	 */
	public function getLastPlayed();

	/**
	 * @return bool
	 */
	public function hasPlayedBefore() : bool;

}
