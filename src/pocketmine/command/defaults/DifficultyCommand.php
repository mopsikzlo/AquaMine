<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\event\TranslationContainer;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\Server;

use function count;

class DifficultyCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.difficulty.description",
			"%commands.difficulty.usage"
		);
		$this->setPermission("pocketmine.command.difficulty");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(count($args) !== 1){
			throw new InvalidCommandSyntaxException();
		}

		$difficulty = Server::getDifficultyFromString($args[0]);

		if($sender->getServer()->isHardcore()){
			$difficulty = 3;
		}

		if($difficulty !== -1){
			$sender->getServer()->setConfigInt("difficulty", $difficulty);

			$pk = new SetDifficultyPacket();
			$pk->difficulty = $sender->getServer()->getDifficulty();
			$sender->getServer()->broadcastPacket($sender->getServer()->getOnlinePlayers(), $pk);

			Command::broadcastCommandMessage($sender, new TranslationContainer("commands.difficulty.success", [$difficulty]));
		}else{
			throw new InvalidCommandSyntaxException();
		}

		return true;
	}
}