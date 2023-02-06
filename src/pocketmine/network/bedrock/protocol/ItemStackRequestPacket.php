<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\bedrock\protocol\types\itemStack\AutoCraftRecipeStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\BeaconPaymentStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\ConsumeStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\CraftCreativeStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\CraftNonImplementedStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\CraftRecipeStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\CraftResultsDeprecatedStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\CreateStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\DestroyStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\DropStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\LabTableCombineStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\MineBlockStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\PlaceStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\StackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\SwapStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\TakeStackRequestAction;
use pocketmine\network\NetworkSession;
use UnexpectedValueException;
use function count;

class ItemStackRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ITEM_STACK_REQUEST_PACKET;

	public const ACTION_TAKE = 0;
	public const ACTION_PLACE = 1;
	public const ACTION_SWAP = 2;
	public const ACTION_DROP = 3;
	public const ACTION_DESTROY = 4;
	public const ACTION_CONSUME = 5;
	public const ACTION_CREATE = 6;
	public const ACTION_PLACE_INTO_BUNDLE = 7;
	public const ACTION_TAKE_FROM_BUNDLE = 8;
	public const ACTION_LAB_TABLE_COMBINE = 9;
	public const ACTION_BEACON_PAYMENT = 10;
	public const ACTION_MINE_BLOCK = 11;
	public const ACTION_CRAFT_RECIPE = 12;
	public const ACTION_CRAFT_RECIPE_AUTO = 13; //recipe book?
	public const ACTION_CRAFT_CREATIVE = 14;
	public const ACTION_CRAFT_RECIPE_OPTIONAL = 15; //anvil/cartography table rename
	public const ACTION_CRAFT_GRINDSTONE = 16;
	public const ACTION_CRAFT_LOOM = 17;
	public const ACTION_CRAFT_NON_IMPLEMENTED_DEPRECATED_ASK_TY_LAING = 18;
	public const ACTION_CRAFT_RESULTS_DEPRECATED_ASK_TY_LAING = 19; //no idea what this is for

	/** @var int */
	public $requestId;
	/** @var StackRequestAction[] */
	public $actions = [];

	public function decodePayload(){
		$this->requestId = $this->getVarInt();

		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$actionType = $this->getByte();

			switch($actionType){
				case self::ACTION_TAKE:
					$action = new TakeStackRequestAction();
					break;
				case self::ACTION_PLACE:
					$action = new PlaceStackRequestAction();
					break;
				case self::ACTION_SWAP:
					$action = new SwapStackRequestAction();
					break;
				case self::ACTION_DROP:
					$action = new DropStackRequestAction();
					break;
				case self::ACTION_DESTROY:
					$action = new DestroyStackRequestAction();
					break;
				case self::ACTION_CONSUME:
					$action = new ConsumeStackRequestAction();
					break;
				case self::ACTION_CREATE:
					$action = new CreateStackRequestAction();
					break;
				case self::ACTION_LAB_TABLE_COMBINE:
					$action = new LabTableCombineStackRequestAction();
					break;
				case self::ACTION_BEACON_PAYMENT:
					$action = new BeaconPaymentStackRequestAction();
					break;
				case self::ACTION_MINE_BLOCK:
					$action = new MineBlockStackRequestAction();
					break;
				case self::ACTION_CRAFT_RECIPE:
					$action = new CraftRecipeStackRequestAction();
					break;
				case self::ACTION_CRAFT_RECIPE_AUTO:
					$action = new AutoCraftRecipeStackRequestAction();
					break;
				case self::ACTION_CRAFT_CREATIVE:
					$action = new CraftCreativeStackRequestAction();
					break;
				case self::ACTION_CRAFT_NON_IMPLEMENTED_DEPRECATED_ASK_TY_LAING:
					$action = new CraftNonImplementedStackRequestAction();
					break;
				case self::ACTION_CRAFT_RESULTS_DEPRECATED_ASK_TY_LAING:
					$action = new CraftResultsDeprecatedStackRequestAction();
					break;
				case self::ACTION_PLACE_INTO_BUNDLE:
				case self::ACTION_TAKE_FROM_BUNDLE:
				case self::ACTION_CRAFT_RECIPE_OPTIONAL:
				case self::ACTION_CRAFT_GRINDSTONE:
				case self::ACTION_CRAFT_LOOM:
					// TODO
					continue 2;
				default:
					throw new UnexpectedValueException("Unknown item stack request action type {$actionType}");
			}

			$action->decode($this);
		}
	}

	public function encodePayload(){
		$this->putVarInt($this->requestId);

		$this->putUnsignedVarInt(count($this->actions));
		foreach($this->actions as $action){
			$this->putByte($action->getActionId());
			$action->encode($this);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleItemStackRequest($this);
	}
}