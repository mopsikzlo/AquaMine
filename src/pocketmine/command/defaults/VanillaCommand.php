<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use function substr;

abstract class VanillaCommand extends Command{
	public const MAX_COORD = 30000000;
	public const MIN_COORD = -30000000;

	public function __construct($name, $description = "", $usageMessage = null, array $aliases = []){
		parent::__construct($name, $description, $usageMessage, $aliases);
	}

	protected function getInteger(CommandSender $sender, $value, $min = self::MIN_COORD, $max = self::MAX_COORD){
		$i = (int) $value;

		if($i < $min){
			$i = $min;
		}elseif($i > $max){
			$i = $max;
		}

		return $i;
	}

	protected function getRelativeDouble($original, CommandSender $sender, $input, $min = self::MIN_COORD, $max = self::MAX_COORD){
		if($input{0} === "~"){
			$value = $this->getDouble($sender, substr($input, 1));

			return $original + $value;
		}

		return $this->getDouble($sender, $input, $min, $max);
	}

	protected function getDouble(CommandSender $sender, $value, $min = self::MIN_COORD, $max = self::MAX_COORD){
		$i = (double) $value;

		if($i < $min){
			$i = $min;
		}elseif($i > $max){
			$i = $max;
		}

		return $i;
	}
}