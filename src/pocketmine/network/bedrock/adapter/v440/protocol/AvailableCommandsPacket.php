<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v440\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\bedrock\protocol\types\CommandData;
use pocketmine\network\bedrock\protocol\types\CommandParameter;
use function array_search;
use function count;
use function dechex;

class AvailableCommandsPacket extends \pocketmine\network\bedrock\protocol\AvailableCommandsPacket {

	protected function getCommandData() : CommandData{
		$retval = new CommandData();
		$retval->commandName = $this->getString();
		$retval->commandDescription = $this->getString();
		$retval->flags = $this->getByte();
		$retval->permission = $this->getByte();
		$retval->aliases = $this->enums[$this->getLInt()] ?? null;

		for($overloadIndex = 0, $overloadCount = $this->getUnsignedVarInt(); $overloadIndex < $overloadCount; ++$overloadIndex){
			for($paramIndex = 0, $paramCount = $this->getUnsignedVarInt(); $paramIndex < $paramCount; ++$paramIndex){
				$parameter = new CommandParameter();
				$parameter->paramName = $this->getString();
				$parameter->paramType = $this->getLInt();
				$parameter->isOptional = $this->getBool();

				if($parameter->paramType & self::ARG_FLAG_ENUM){
					$index = ($parameter->paramType & 0xffff);
					$parameter->enum = $this->enums[$index] ?? null;
					if($parameter->enum === null){
						throw new \UnexpectedValueException("expected enum at $index, but got none");
					}
				}elseif($parameter->paramType & self::ARG_FLAG_POSTFIX){
					$index = ($parameter->paramType & 0xffff);
					$parameter->postfix = $this->postfixes[$index] ?? null;
					if($parameter->postfix === null){
						throw new \UnexpectedValueException("expected postfix at $index, but got none");
					}
				}elseif(($parameter->paramType & self::ARG_FLAG_VALID) === 0){
					throw new \UnexpectedValueException("Invalid parameter type 0x" . dechex($parameter->paramType));
				}

				$retval->overloads[$overloadIndex][$paramIndex] = $parameter;
			}
		}

		return $retval;
	}

	protected function putCommandData(CommandData $data) : void{
		$this->putString($data->commandName);
		$this->putString($data->commandDescription);
		$this->putByte($data->flags);
		$this->putByte($data->permission);

		if($data->aliases !== null){
			$this->putLInt($this->enumMap[$data->aliases->enumName] ?? -1);
		}else{
			$this->putLInt(-1);
		}

		$this->putUnsignedVarInt(count($data->overloads));
		foreach($data->overloads as $overload){
			/** @var CommandParameter[] $overload */
			$this->putUnsignedVarInt(count($overload));
			foreach($overload as $parameter){
				$this->putString($parameter->paramName);

				if($parameter->enum !== null){
					$type = self::ARG_FLAG_ENUM | self::ARG_FLAG_VALID | ($this->enumMap[$parameter->enum->enumName] ?? -1);
				}elseif($parameter->postfix !== null){
					$key = array_search($parameter->postfix, $this->postfixes, true);
					if($key === false){
						throw new \InvalidStateException("Postfix '$parameter->postfix' not in postfixes array");
					}
					$type = self::ARG_FLAG_POSTFIX | $key;
				}else{
					$type = $parameter->paramType;
				}

				$this->putLInt($type);
				$this->putBool($parameter->isOptional);
				$this->putByte(0); // TODO: 19/03/2019 Bit flags. Only first bit is used for GameRules.
			}
		}
	}
}
