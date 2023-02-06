<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>


use pocketmine\network\NetworkSession;

class SimulationTypePacket extends DataPacket{
    public const NETWORK_ID = ProtocolInfo::SIMULATION_TYPE_PACKET;
    public const GAME = 0;
    public const EDITOR = 1;
    public const TEST = 2;

    /** @var int */
    public $type;

    public function decodePayload() : void{
        $this->type = $this->getByte();
    }

    public function encodePayload() : void{
        $this->putByte($this->type);
    }

    public function handle(NetworkSession $session): bool {
        return $session->handleSimulationType($this);
    }
}