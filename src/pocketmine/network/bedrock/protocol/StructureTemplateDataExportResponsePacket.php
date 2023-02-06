<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class StructureTemplateDataExportResponsePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::STRUCTURE_TEMPLATE_DATA_EXPORT_RESPONSE_PACKET;

	/** @var string */
	public $string;
	/** @var bool */
	public $response;
	/** @var string */
	public $namedtag;

	public function decodePayload(){
		$this->string = $this->getString();
		$this->response = $this->getBool();
		if($this->response){
			$this->namedtag = $this->getRemaining();
		}
	}

	public function encodePayload(){
		$this->putString($this->string);
		$this->putBool($this->response);
		if($this->response){
			$this->put($this->namedtag);
		}
	}

	public function mustBeDecoded() : bool{
		return false;
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleStructureTemplateDataExportResponse($this);
	}
}
