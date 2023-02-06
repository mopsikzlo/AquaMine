<?php

declare(strict_types=1);

/**
 * Network-related classes
 */
namespace pocketmine\network;

use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\Player;

/**
 * Classes that implement this interface will be able to be attached to players
 */
interface NetworkInterface{

	/**
	 * Sends a DataPacket to the interface, returns an unique identifier for the packet if $needACK is true
	 *
	 * @param Player     $player
	 * @param DataPacket $packet
	 * @param bool       $needACK
	 * @param bool       $immediate
	 *
	 * @return int|null
	 */
	public function putPacket(Player $player, DataPacket $packet, bool $needACK = false, bool $immediate = true);

	/**
	 * Sends a raw buffer to the interface, returns an unique identifier for the packet if $needACK is true
	 *
	 * @param Player     $player
	 * @param DataPacket $packet
	 * @param bool       $needACK
	 * @param bool       $immediate
	 *
	 * @return int|null
	 */
	public function putBuffer(Player $player, string $buffer, bool $needACK = false, bool $immediate = true);

	/**
	 * Terminates the connection
	 *
	 * @param Player $player
	 * @param string $reason
	 */
	public function close(Player $player, string $reason = "unknown reason");

	/**
	 * @return bool
	 */
	public function process() : bool;

	public function shutdown();

	public function emergencyShutdown();

}