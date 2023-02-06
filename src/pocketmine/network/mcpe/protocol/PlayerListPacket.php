<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\network\mcpe\protocol\types\Skin;
use pocketmine\network\NetworkSession;
use function count;

class PlayerListPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_LIST_PACKET;

	public const TYPE_ADD = 0;
	public const TYPE_REMOVE = 1;

	/** @var PlayerListEntry[] */
	public $entries = [];
	/** @var int */
	public $type;

	public function clean(){
		$this->entries = [];
		return parent::clean();
	}

	public function decodePayload(){
		$this->type = $this->getByte();
		$count = $this->getUnsignedVarInt();
		for($i = 0; $i < $count; ++$i){
			$entry = new PlayerListEntry();

			if($this->type === self::TYPE_ADD){
				$entry->uuid = $this->getUUID();
				$entry->entityUniqueId = $this->getEntityUniqueId();
				$entry->username = $this->getString();

				$skinId = $this->getString();
				$skinData = $this->getString();
				$entry->skin = new Skin($skinId, $skinData);
			}else{
				$entry->uuid = $this->getUUID();
			}

			$this->entries[$i] = $entry;
		}
	}

	public function encodePayload(){
		$this->putByte($this->type);
		$this->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			if($this->type === self::TYPE_ADD){
				$this->putUUID($entry->uuid);
				$this->putEntityUniqueId($entry->entityUniqueId);
				$this->putString($entry->username);
				$this->putString($entry->skin->getSkinId());
				$this->putString($entry->skin->getSkinData());
			}else{
				$this->putUUID($entry->uuid);
			}
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerList($this);
	}

}
