<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v448\protocol;

#include <rules/DataPacket.h>


use pocketmine\inventory\FurnaceRecipe;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Item;
use pocketmine\network\bedrock\palette\ItemPalette;
use pocketmine\network\bedrock\protocol\types\PotionContainerChangeRecipe;
use pocketmine\network\bedrock\protocol\types\PotionTypeRecipe;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\NetworkSession;
use function count;

class CraftingDataPacket extends \pocketmine\network\bedrock\protocol\CraftingDataPacket {
    use PacketTrait;

	public function decodePayload(){
		$entries = [];
		$recipeCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $recipeCount; ++$i){
			$entry = [];
			$entry["type"] = $recipeType = $this->getVarInt();

			switch($recipeType){
				case self::ENTRY_SHAPELESS:
				case self::ENTRY_SHULKER_BOX:
					$entry["recipe_id"] = $this->getString();
					$ingredientCount = $this->getUnsignedVarInt();
					/** @var Item */
					$entry["input"] = [];
					for($j = 0; $j < $ingredientCount; ++$j){
						$entry["input"][] = $this->readRecipeIngredient();
					}
					$resultCount = $this->getUnsignedVarInt();
					$entry["output"] = [];
					for($k = 0; $k < $resultCount; ++$k){
						$entry["output"][] = $this->getItemStackWithoutStackId();
					}
					$entry["uuid"] = $this->getUUID()->toString();
					$entry["craftingFlag"] = $this->getString();
					$entry["priority"] = $this->getVarInt();

					break;
				case self::ENTRY_SHAPED:
					$entry["recipe_id"] = $this->getString();
					$entry["width"] = $this->getVarInt();
					$entry["height"] = $this->getVarInt();
					$count = $entry["width"] * $entry["height"];
					$entry["input"] = [];
					for($j = 0; $j < $count; ++$j){
						$entry["input"][] = $this->readRecipeIngredient();
					}
					$resultCount = $this->getUnsignedVarInt();
					$entry["output"] = [];
					for($k = 0; $k < $resultCount; ++$k){
						$entry["output"][] = $this->getItemStackWithoutStackId();
					}
					$entry["uuid"] = $this->getUUID()->toString();
					$entry["craftingFlag"] = $this->getString();
					$entry["priority"] = $this->getVarInt();
					break;
				case self::ENTRY_FURNACE:
				case self::ENTRY_FURNACE_DATA:
					$inputIdNet = $this->getVarInt();
					if($recipeType === self::ENTRY_FURNACE){
						[$inputId, $inputData] = ItemPalette::getLegacyFromRuntimeIdWildcard($inputIdNet, 0x7fff);
					}else{
						$inputMetaNet = $this->getVarInt();
						[$inputId, $inputData] = ItemPalette::getLegacyFromRuntimeIdWildcard($inputIdNet, $inputMetaNet);
					}

					$entry["input"] = Item::get($inputId, $inputData);
					$entry["output"] = $out = $this->getItemStackWithoutStackId();
					if($out->getDamage() === 0x7fff){
						$out->setDamage(0); //TODO HACK: some 1.12 furnace recipe outputs have wildcard damage values
					}
					$entry["craftingFlag"] = $this->getString();
					break;
				case self::ENTRY_MULTI:
					$entry["uuid"] = $this->getUUID()->toString();
					$entry["craftingFlag"] = $this->getString();
					break;
				default:
					throw new \UnexpectedValueException("Unhandled recipe type $recipeType!"); //do not continue attempting to decode
			}
			$entries[] = $entry;
		}
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$inputIdNet = $this->getVarInt();
			$inputMetaNet = $this->getVarInt();
			[$inputId, $inputMeta] = ItemPalette::getLegacyFromRuntimeId($inputIdNet, $inputMetaNet);
			$ingredientIdNet = $this->getVarInt();
			$ingredientMetaNet = $this->getVarInt();
			[$ingredientId, $ingredientMeta] = ItemPalette::getLegacyFromRuntimeId($ingredientIdNet, $ingredientMetaNet);
			$outputIdNet = $this->getVarInt();
			$outputMetaNet = $this->getVarInt();
			[$outputId, $outputMeta] = ItemPalette::getLegacyFromRuntimeId($outputIdNet, $outputMetaNet);
			$this->potionTypeRecipes[] = new PotionTypeRecipe($inputId, $inputMeta, $ingredientId, $ingredientMeta, $outputId, $outputMeta);
		}

		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			//TODO: we discard inbound ID here, not safe because netID on its own might map to internalID+internalMeta for us
			$inputIdNet = $this->getVarInt();
			[$input, ] = ItemPalette::getLegacyFromRuntimeId($inputIdNet, 0);
			$ingredientIdNet = $this->getVarInt();
			[$ingredient, ] = ItemPalette::getLegacyFromRuntimeId($ingredientIdNet, 0);
			$outputIdNet = $this->getVarInt();
			[$output, ] = ItemPalette::getLegacyFromRuntimeId($outputIdNet, 0);
			$this->potionContainerRecipes[] = new PotionContainerChangeRecipe($input, $ingredient, $output);
		}

		$this->cleanRecipes = $this->getBool();
	}

	public function encodePayload(){
		$this->putUnsignedVarInt(count($this->entries));

		$networkId = 1;
		foreach($this->entries as $d){
			$this->writeEntry($d, $networkId++);
		}

		$this->putUnsignedVarInt(count($this->potionTypeRecipes));
		foreach($this->potionTypeRecipes as $entry){
			$this->putVarInt($entry->getInputPotionId());
			$this->putVarInt($entry->getInputPotionMeta());
			$this->putVarInt($entry->getIngredientItemId());
			$this->putVarInt($entry->getIngredientItemMeta());
			$this->putVarInt($entry->getOutputPotionId());
			$this->putVarInt($entry->getOutputPotionMeta());
		}

		$this->putUnsignedVarInt(count($this->potionContainerRecipes));
		foreach($this->potionContainerRecipes as $entry){
			$this->putVarInt($entry->getInputItemId());
			$this->putVarInt($entry->getIngredientItemId());
			$this->putVarInt($entry->getOutputItemId());
		}

		$this->putBool($this->cleanRecipes);
	}
}
