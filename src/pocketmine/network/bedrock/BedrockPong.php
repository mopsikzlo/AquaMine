<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock;

use pocketmine\network\mcpe\McpePong;
use function addcslashes;
use function array_map;
use function array_merge;
use function array_slice;
use function count;
use function explode;
use function implode;
use function rtrim;

class BedrockPong extends McpePong{

	/** @var bool */
	protected $nintendoLimited = false;
	/** @var int */
	protected $ipv4Port = -1;
	/** @var int */
	protected $ipv6Port = -1;

	/**
	 * @param string $data
	 *
	 * @return self
	 */
	public static function fromServerName(string $data) : McpePong{
		$fields = explode(";", $data);

		$pong = new self;
		$pong->edition = $fields[0] ?? "";
		$pong->motd = $fields[1] ?? "";
		$pong->protocolVersion = (int) ($fields[2] ?? -1);
		$pong->minecraftVersion = $fields[3] ?? "";
		$pong->playerCount = (int) ($fields[4] ?? -1);
		$pong->maxPlayerCount = (int) ($fields[5] ?? -1);
		$pong->serverId = (int) ($fields[6] ?? -1);
		$pong->subMotd = $fields[7] ?? "";
		$pong->gameType = $fields[8] ?? "";
		$pong->nintendoLimited = ($fields[9] ?? "1") !== "1";
		$pong->ipv4Port = (int) ($fields[10] ?? -1);
		$pong->ipv6Port = (int) ($fields[11] ?? -1);

		if(count($fields) > 12){
			$pong->extraData = array_slice($fields, 12);
		}
		return $pong;
	}

	public function toServerName() : string{
		return implode(";", array_map(function(string $str) : string{
			return rtrim(addcslashes($str, ";"), '\\');
		}, array_merge([
				$this->edition,
				$this->motd,
				(string) $this->protocolVersion,
				$this->minecraftVersion,
				(string) $this->playerCount,
				(string) $this->maxPlayerCount,
				(string) $this->serverId,
				$this->subMotd,
				$this->gameType,
				$this->nintendoLimited ? "0" : "1",
				(string) $this->ipv4Port,
				(string) $this->ipv6Port
			], $this->extraData)
		));
	}

	/**
	 * @return bool
	 */
	public function isNintendoLimited() : bool{
		return $this->nintendoLimited;
	}

	/**
	 * @param bool $nintendoLimited
	 */
	public function setNintendoLimited(bool $nintendoLimited) : void{
		$this->nintendoLimited = $nintendoLimited;
	}

	/**
	 * @return int
	 */
	public function getIpv4Port() : int{
		return $this->ipv4Port;
	}

	/**
	 * @param int $ipv4Port
	 */
	public function setIpv4Port(int $ipv4Port) : void{
		$this->ipv4Port = $ipv4Port;
	}

	/**
	 * @return int
	 */
	public function getIpv6Port() : int{
		return $this->ipv6Port;
	}

	/**
	 * @param int $ipv6Port
	 */
	public function setIpv6Port(int $ipv6Port) : void{
		$this->ipv6Port = $ipv6Port;
	}
}