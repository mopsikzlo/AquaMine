<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\bedrock\protocol\ProtocolInfo;
use pocketmine\network\NetworkSession;

class NpcDialoguePacket extends DataPacket{
    public const NETWORK_ID = ProtocolInfo::NPC_DIALOGUE_PACKET;

    public const ACTION_OPEN = 0;
    public const ACTION_CLOSE = 1;

    /** @var int */
    public $npcActorUniqueId;
    /** @var int */
    public $actionType;
    /** @var string */
    public $dialogue;
    /** @var string */
    public $sceneName;
    /** @var string */
    public $npcName;
    /** @var string */
    public $actionJson;

    public function decodePayload() : void{
        $this->npcActorUniqueId = $this->getEntityUniqueId();
        $this->actionType = $this->getVarInt();
        $this->dialogue = $this->getString();
        $this->sceneName = $this->getString();
        $this->npcName = $this->getString();
        $this->actionJson = $this->getString();
    }

    public function encodePayload() : void{
        $this->putEntityUniqueId($this->npcActorUniqueId);
        $this->putVarInt($this->actionType);
        $this->putString($this->dialogue);
        $this->putString($this->sceneName);
        $this->putString($this->npcName);
        $this->putString($this->actionJson);
    }

    public function handle(NetworkSession $session) : bool{
        return $session->handleNpcDialogue($this);
    }
}