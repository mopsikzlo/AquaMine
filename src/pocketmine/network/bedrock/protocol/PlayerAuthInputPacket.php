<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\network\bedrock\protocol\types\PlayMode;
use pocketmine\network\NetworkSession;

class PlayerAuthInputPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_AUTH_INPUT_PACKET;

	public const INPUT_ASCEND = 0;
	public const INPUT_DESCEND = 1;
	public const INPUT_NORTH_JUMP = 2;
	public const INPUT_JUMP_DOWN = 3;
	public const INPUT_SPRINT_DOWN = 4;
	public const INPUT_CHANGE_HEIGHT = 5;
	public const INPUT_JUMPING = 6;
	public const INPUT_AUTO_JUMPING_INWATER = 7;
	public const INPUT_SNEAKING = 8;
	public const INPUT_SNEAK_DOWN = 9;
	public const INPUT_UP = 10;
	public const INPUT_DOWN = 11;
	public const INPUT_LEFT = 12;
	public const INPUT_RIGHT = 13;
	public const INPUT_UP_LEFT = 14;
	public const INPUT_UP_RIGHT = 15;
	public const INPUT_WANT_UP = 16;
	public const INPUT_WANT_DOWN = 17;
	public const INPUT_WANT_DOWN_SLOW = 18;
	public const INPUT_WANT_UP_SLOW = 19;
	public const INPUT_SPRINTING = 20;
	public const INPUT_ASCEND_SCAFFOLDING = 21;
	public const INPUT_DESCEND_SCAFFOLDING = 22;
	public const INPUT_SNEAK_TOGGLE_DOWN = 23;
	public const INPUT_PERSIST_SNEAK = 24;

	public const INTERACTION_TOUCH = 0;
	public const INTERACTION_CROSSHAIR = 1;
	public const INTERACTION_CLASSIC = 2; //???

	/** @var float */
	public $yaw;
	/** @var float */
	public $pitch;
	/** @var Vector3 */
	public $playerMovePosition;
	/** @var Vector2 */
	public $motion;
	/** @var float */
	public $headRotation;
	/** @var int */
	public $inputFlags;
	/** @var int */
	public $inputMode;
	/** @var int */
	public $playMode;
	/** @var int */
	public $interactionMode;
	/** @var Vector3|null */
	public $vrGazeDirection;
	/** @var int */
	public $tick;
	/** @var Vector3 */
	public $delta;

	public function decodePayload(){
		$this->yaw = $this->getLFloat();
		$this->pitch = $this->getLFloat();
		$this->playerMovePosition = $this->getVector3();
		$this->motion = $this->getVector2();
		$this->headRotation = $this->getLFloat();
		$this->inputFlags = $this->getUnsignedVarLong();
		$this->inputMode = $this->getUnsignedVarInt();
		$this->playMode = $this->getUnsignedVarInt();
		$this->interactionMode = $this->getUnsignedVarInt();
		if($this->playMode === PlayMode::VR){
			$this->vrGazeDirection = $this->getVector3();
		}
		$this->tick = $this->getUnsignedVarLong();
		$this->delta = $this->getVector3();
	}

	public function encodePayload(){
		$this->putLFloat($this->yaw);
		$this->putLFloat($this->pitch);
		$this->putVector3($this->playerMovePosition);
		$this->putVector2($this->motion);
		$this->putLFloat($this->headRotation);
		$this->putUnsignedVarLong($this->inputFlags);
		$this->putUnsignedVarInt($this->inputMode);
		$this->putUnsignedVarInt($this->playMode);
		$this->putUnsignedVarInt($this->interactionMode);
		if($this->playMode === PlayMode::VR){
			$this->putVector3($this->vrGazeDirection);
		}
		$this->putUnsignedVarLong($this->tick);
		$this->putVector3($this->delta);
	}

	public function getInputFlag(int $flag) : bool{
		return ($this->inputFlags & (1 << $flag)) !== 0;
	}

	public function setInputFlag(int $flag, bool $value) : void{
		if($value){
			$this->inputFlags |= (1 << $flag);
		}else{
			$this->inputFlags &= ~(1 << $flag);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerAuthInput($this);
	}
}
