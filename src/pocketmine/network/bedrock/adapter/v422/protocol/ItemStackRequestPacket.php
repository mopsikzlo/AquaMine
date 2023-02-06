<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v422\protocol;

#include <rules/DataPacket.h>


use InvalidArgumentException;
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
use pocketmine\network\bedrock\protocol\types\itemStack\SwapStackRequestAction;
use pocketmine\network\bedrock\protocol\types\itemStack\TakeStackRequestAction;
use UnexpectedValueException;
use function count;

class ItemStackRequestPacket extends \pocketmine\network\bedrock\protocol\ItemStackRequestPacket{

	public const ACTION_TAKE = 0;
	public const ACTION_PLACE = 1;
	public const ACTION_SWAP = 2;
	public const ACTION_DROP = 3;
	public const ACTION_DESTROY = 4;
	public const ACTION_CONSUME = 5;
	public const ACTION_CREATE = 6;
	public const ACTION_LAB_TABLE_COMBINE = 7;
	public const ACTION_BEACON_PAYMENT = 8;
	public const ACTION_CRAFT_RECIPE = 9;
	public const ACTION_CRAFT_RECIPE_AUTO = 10;
	public const ACTION_CRAFT_CREATIVE = 11;
	public const ACTION_CRAFT_NON_IMPLEMENTED_DEPRECATED = 12;
	public const ACTION_CRAFT_RESULTS_DEPRECATED = 13;

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
				case self::ACTION_CRAFT_RECIPE:
					$action = new CraftRecipeStackRequestAction();
					break;
				case self::ACTION_CRAFT_RECIPE_AUTO:
					$action = new AutoCraftRecipeStackRequestAction();
					break;
				case self::ACTION_CRAFT_CREATIVE:
					$action = new CraftCreativeStackRequestAction();
					break;
				case self::ACTION_CRAFT_NON_IMPLEMENTED_DEPRECATED:
					$action = new CraftNonImplementedStackRequestAction();
					break;
				case self::ACTION_CRAFT_RESULTS_DEPRECATED:
					$action = new CraftResultsDeprecatedStackRequestAction();
					break;
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
			$actionId = $action->getActionId();
			if($actionId === parent::ACTION_MINE_BLOCK){
				throw new InvalidArgumentException("Unsupported action ID for protocol 422: {$actionId}");
			}elseif($actionId > parent::ACTION_MINE_BLOCK){
				--$actionId;
			}

			$this->putByte($actionId);
			$action->encode($this);
		}
	}
}