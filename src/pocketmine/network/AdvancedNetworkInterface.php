<?php

declare(strict_types=1);

/**
 * Network-related classes
 */
namespace pocketmine\network;

interface AdvancedNetworkInterface extends NetworkInterface{

	/**
	 * @param string $address
	 * @param int    $timeout Seconds
	 */
	public function blockAddress(string $address, int $timeout = 300);

	/**
	 * @param Network $network
	 */
	public function setNetwork(Network $network);

	/**
	 * @param string $address
	 * @param int    $port
	 * @param string $payload
	 */
	public function sendRawPacket(string $address, int $port, string $payload);

}