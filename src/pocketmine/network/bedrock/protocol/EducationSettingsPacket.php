<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\NetworkSession;

class EducationSettingsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::EDUCATION_SETTINGS_PACKET;

	/** @var string */
	public $codeBuilderDefaultUri;
	/** @var string */
	public $codeBuilderTitle;
	/** @var bool */
	public $canResizeCodeBuilder;
	/** @var bool */
	public $optionalOverrideUri;
	/** @var string */
	public $overrideUri;
	/** @var bool */
	public $hasQuiz;

	public function decodePayload(){
		$this->codeBuilderDefaultUri = $this->getString();
		$this->codeBuilderTitle = $this->getString();
		$this->canResizeCodeBuilder = $this->getBool();
		$this->optionalOverrideUri = $this->getBool();
		if($this->optionalOverrideUri){
			$this->overrideUri = $this->getString();
		}
		$this->hasQuiz = $this->getBool();
	}

	public function encodePayload(){
		$this->putString($this->codeBuilderDefaultUri);
		$this->putString($this->codeBuilderTitle);
		$this->putBool($this->canResizeCodeBuilder);
		$this->putBool($this->optionalOverrideUri);
		if($this->optionalOverrideUri){
			$this->putString($this->overrideUri);
		}
		$this->putBool($this->hasQuiz);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleEducationSettings($this);
	}
}