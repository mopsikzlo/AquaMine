<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\bedrock\protocol\types\StructureSettings;
use pocketmine\network\NetworkSession;

class StructureTemplateDataExportRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::STRUCTURE_TEMPLATE_DATA_EXPORT_REQUEST_PACKET;

	/** @var string */
	public $string;
	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var StructureSettings */
	public $structureSettings;
	/** @var int */
	public $byte;

	public function decodePayload(){
		$this->string = $this->getString();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->structureSettings = $this->getStructureSettings();
		$this->byte = $this->getByte();
	}

	public function encodePayload(){
		$this->putString($this->string);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putStructureSettings($this->structureSettings);
		$this->putByte($this->byte);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleStructureTemplateDataExportRequest($this);
	}
}
