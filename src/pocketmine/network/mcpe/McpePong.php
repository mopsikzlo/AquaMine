<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

use function addcslashes;
use function array_map;
use function array_merge;
use function array_slice;
use function count;
use function explode;
use function implode;
use function rtrim;

class McpePong{

	/** @var string */
	protected $edition = "";
	/** @var string */
	protected $motd = "";
	/** @var int */
	protected $protocolVersion = -1;
	/** @var string */
	protected $minecraftVersion = "";
	/** @var int */
	protected $playerCount = -1;
	/** @var int */
	protected $maxPlayerCount = -1;
	/** @var int */
	protected $serverId = -1;
	/** @var string */
	protected $subMotd = "";
	/** @var string */
	protected $gameType = "";
	/** @var string[] */
	protected $extraData = [];

	public static function fromServerName(string $data) : self{
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

		if(count($fields) > 9){
			$pong->extraData = array_slice($fields, 9);
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
				$this->gameType
			], $this->extraData)
		));
	}

	/**
	 * @return string
	 */
	public function getEdition() : string{
		return $this->edition;
	}

	/**
	 * @param string $edition
	 */
	public function setEdition(string $edition) : void{
		$this->edition = $edition;
	}

	/**
	 * @return string
	 */
	public function getMotd() : string{
		return $this->motd;
	}

	/**
	 * @param string $motd
	 */
	public function setMotd(string $motd) : void{
		$this->motd = $motd;
	}

	/**
	 * @return int
	 */
	public function getProtocolVersion() : int{
		return $this->protocolVersion;
	}

	/**
	 * @param int $protocolVersion
	 */
	public function setProtocolVersion(int $protocolVersion) : void{
		$this->protocolVersion = $protocolVersion;
	}

	/**
	 * @return string
	 */
	public function getMinecraftVersion() : string{
		return $this->minecraftVersion;
	}

	/**
	 * @param string $minecraftVersion
	 */
	public function setMinecraftVersion(string $minecraftVersion) : void{
		$this->minecraftVersion = $minecraftVersion;
	}

	/**
	 * @return int
	 */
	public function getPlayerCount() : int{
		return $this->playerCount;
	}

	/**
	 * @param int $playerCount
	 */
	public function setPlayerCount(int $playerCount) : void{
		$this->playerCount = $playerCount;
	}

	/**
	 * @return int
	 */
	public function getMaxPlayerCount() : int{
		return $this->maxPlayerCount;
	}

	/**
	 * @param int $maxPlayerCount
	 */
	public function setMaxPlayerCount(int $maxPlayerCount) : void{
		$this->maxPlayerCount = $maxPlayerCount;
	}

	/**
	 * @return int
	 */
	public function getServerId() : int{
		return $this->serverId;
	}

	/**
	 * @param int $serverId
	 */
	public function setServerId(int $serverId) : void{
		$this->serverId = $serverId;
	}

	/**
	 * @return string
	 */
	public function getSubMotd() : string{
		return $this->subMotd;
	}

	/**
	 * @param string $subMotd
	 */
	public function setSubMotd(string $subMotd) : void{
		$this->subMotd = $subMotd;
	}

	/**
	 * @return string
	 */
	public function getGameType() : string{
		return $this->gameType;
	}

	/**
	 * @param string $gameType
	 */
	public function setGameType(string $gameType) : void{
		$this->gameType = $gameType;
	}

	/**
	 * @return string[]
	 */
	public function getExtraData() : array{
		return $this->extraData;
	}

	/**
	 * @param string[] $extraData
	 */
	public function setExtraData(array $extraData) : void{
		$this->extraData = $extraData;
	}
}