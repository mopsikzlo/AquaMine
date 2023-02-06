<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v407\protocol;

#include <rules/DataPacket.h>


use pocketmine\inventory\FurnaceRecipe;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\Item;

class CraftingDataPacket extends \pocketmine\network\bedrock\adapter\v448\protocol\CraftingDataPacket {
	use PacketTrait;

	protected function writeEntry($entry, int $index) : void{
		if($entry instanceof ShapelessRecipe){
			$this->putVarInt(self::ENTRY_SHAPELESS);
			$this->writeShapelessRecipe($entry, $index);
		}elseif($entry instanceof ShapedRecipe){
			$this->putVarInt(self::ENTRY_SHAPED);
			$this->writeShapedRecipe($entry, $index);
		}elseif($entry instanceof FurnaceRecipe){
			if(!$entry->getInput()->hasAnyDamageValue()){
				$this->putVarInt(self::ENTRY_FURNACE);
				$this->writeFurnaceRecipe($entry);
			}else{
				$this->putVarInt(self::ENTRY_FURNACE_DATA);
				$this->writeFurnaceRecipeData($entry);
			}
		}else{
			$this->putVarInt(-1);
		}
	}

	protected function writeShapelessRecipe(ShapelessRecipe $recipe, int $networkId) : void{
		$this->putString($recipe->getId()->toString());
		$this->putUnsignedVarInt($recipe->getIngredientCount());
		foreach($recipe->getIngredientList() as $item){
			$this->writeRecipeIngredient($item);
		}

		$this->putUnsignedVarInt(1);
		$this->putItemStackWithoutStackId($recipe->getResult());

		$this->putUUID($recipe->getId());
		$this->putString(self::CRAFTING_TAG_CRAFTING_TABLE);

		$this->putVarInt(50); //priority (???)
		$this->putUnsignedVarInt($networkId);
	}

	protected function writeShapedRecipe(ShapedRecipe $recipe, int $networkId) : void{
		$this->putString($recipe->getId()->toString());
		$this->putVarInt($recipe->getWidth());
		$this->putVarInt($recipe->getHeight());

		for($z = 0; $z < $recipe->getHeight(); ++$z){
			for($x = 0; $x < $recipe->getWidth(); ++$x){
				$this->writeRecipeIngredient($recipe->getIngredient($x, $z));
			}
		}

		$this->putUnsignedVarInt(1);
		$this->putItemStackWithoutStackId($recipe->getResult());

		$this->putUUID($recipe->getId());
		$this->putString(self::CRAFTING_TAG_CRAFTING_TABLE);

		$this->putVarInt(50); //priority (???)
		$this->putUnsignedVarInt($networkId);
	}

	protected function writeFurnaceRecipe(FurnaceRecipe $recipe) : void{
		$this->putVarInt($recipe->getInput()->getId());
		$this->putItemStackWithoutStackId($recipe->getResult());
		$this->putString(self::CRAFTING_TAG_FURNACE);
	}
	
	protected function writeFurnaceRecipeData(FurnaceRecipe $recipe) : void{
		$this->putVarInt($recipe->getInput()->getId());
		$this->putVarInt($recipe->getInput()->getDamage());
		$this->putItemStackWithoutStackId($recipe->getResult());
		$this->putString(self::CRAFTING_TAG_FURNACE);
	}

	protected function writeRecipeIngredient(Item $item) : void{
		if($item->getId() === 0){
			$this->putVarInt(0);
			return;
		}

		$this->putVarInt($item->getId());
		$this->putVarInt($item->hasAnyDamageValue() ? 0x7fff : $item->getDamage());
		$this->putVarInt($item->getCount());
	}

	protected function readRecipeIngredient() : Item{
		$id = $this->getVarInt();
		if($id === 0){
			return Item::get(0, 0, 0);
		}

		$data = $this->getVarInt();
		if($data === 0x7fff){
			$data = -1;
		}

		$cnt = $this->getVarInt();
		return Item::get($id, $data, $cnt);
	}
}