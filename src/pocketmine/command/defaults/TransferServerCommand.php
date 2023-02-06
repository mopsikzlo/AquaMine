<?php

declare(strict_types=1);


namespace pocketmine\command\defaults;


use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\Player;

use function count;

class TransferServerCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.transferserver.description",
			"%pocketmine.command.transferserver.usage"
		);
		$this->setPermission("pocketmine.command.transferserver");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(count($args) < 1){
			throw new InvalidCommandSyntaxException();
		}elseif(!($sender instanceof Player) and !isset($args[2])){
			$sender->sendMessage("This command must be executed as a player");

			return false;
		}

		if(!($sender instanceof Player)){
			if(!(($player = $sender->getServer()->getPlayerExact($args[2])) instanceof Player)){
				$sender->sendMessage("Invalid player name given.");

				return false;
			}
			$player->transfer($args[0], (int) ($args[1] ?? 19132));
		}else{
			$sender->transfer($args[0], (int) ($args[1] ?? 19132));
		}

		return true;
	}
}
