<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

use function array_shift;
use function count;
use function implode;
use function trim;

class KickCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.kick.description",
			"%commands.kick.usage"
		);
		$this->setPermission("pocketmine.command.kick");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(count($args) === 0){
			throw new InvalidCommandSyntaxException();
		}

		$name = array_shift($args);
		$reason = trim(implode(" ", $args));

		if(($player = $sender->getServer()->getPlayer($name)) instanceof Player){
			$player->kick($reason);
			if($reason !== ""){
				Command::broadcastCommandMessage($sender, new TranslationContainer("commands.kick.success.reason", [$player->getName(), $reason]));
			}else{
				Command::broadcastCommandMessage($sender, new TranslationContainer("commands.kick.success", [$player->getName()]));
			}
		}else{
			$sender->sendCommandMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
		}


		return true;
	}
}
