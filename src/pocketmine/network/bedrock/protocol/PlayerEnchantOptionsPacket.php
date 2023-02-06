<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\bedrock\protocol\types\EnchantmentInstance;
use pocketmine\network\bedrock\protocol\types\EnchantmentOption;
use pocketmine\network\bedrock\protocol\types\ItemEnchantments;
use pocketmine\network\NetworkSession;
use function count;

class PlayerEnchantOptionsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_ENCHANT_OPTIONS_PACKET;

	/** @var EnchantmentOption[] */
	public $options = [];

	public function decodePayload(){
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$option = new EnchantmentOption();
			$option->cost = $this->getUnsignedVarInt();

			$option->enchantments = new ItemEnchantments();
			$option->enchantments->slot = $this->getLInt();
			for($i = 0; $i < 3; ++$i){
				for($j = 0, $count = $this->getUnsignedVarInt(); $j < $count; ++$j){
					$enchantment = new EnchantmentInstance();
					$enchantment->type = $this->getByte();
					$enchantment->level = $this->getByte();

					$option->enchantments->enchantments[$i][$j] = $enchantment;
				}
			}

			$option->name = $this->getString();
			$option->recipeNetworkId = $this->getUnsignedVarInt();
		}
	}

	public function encodePayload(){
		$this->putUnsignedVarInt(count($this->options));
		foreach($this->options as $option){
			$this->putUnsignedVarInt($option->cost);

			$this->putLInt($option->enchantments->slot);
			foreach($option->enchantments->enchantments as $enchantments){
				$this->putUnsignedVarInt(count($enchantments));

				foreach($enchantments as $enchantment){
					$this->putByte($enchantment->type);
					$this->putByte($enchantment->level);
				}
			}

			$this->putString($option->name);
			$this->putUnsignedVarInt($option->recipeNetworkId);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerEnchantOptions($this);
	}
}