<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class ModalFormRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MODAL_FORM_REQUEST_PACKET;

	/** @var int */
	public $formId;
	/** @var string */
	public $formData; //json

	public function decodePayload(){
		$this->formId = $this->getUnsignedVarInt();
		$this->formData = $this->getString();
	}

	public function encodePayload(){
		$this->putUnsignedVarInt($this->formId);
		$this->putString($this->formData);
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleModalFormRequest($this);
	}
}