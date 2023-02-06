<?php

declare(strict_types=1);

namespace pocketmine\tile;

use InvalidArgumentException;
use pocketmine\BedrockPlayer;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\bedrock\utils\BedrockUtils;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Sign extends Spawnable{

	public function __construct(Level $level, CompoundTag $nbt){
		for($i = 1; $i <= 4; ++$i){
			if(!$nbt->hasTag("Text$i")){
				$nbt->setString("Text$i", "");
			}
		}

		parent::__construct($level, $nbt);
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->removeTag("Creator");
	}

	public function setText($line1 = "", $line2 = "", $line3 = "", $line4 = ""){
		$this->namedtag->setString("Text1", $line1);
		$this->namedtag->setString("Text2", $line2);
		$this->namedtag->setString("Text3", $line3);
		$this->namedtag->setString("Text4", $line4);
		$this->onChanged();
	}

	/**
	 * @param int    $index 0-3
	 * @param string $line
	 * @param bool   $update
	 */
	public function setLine(int $index, string $line, bool $update = true){
		if($index < 0 or $index > 3){
			throw new InvalidArgumentException("Index must be in the range 0-3!");
		}
		$this->namedtag->setString("Text" . ($index + 1), $line);
		if($update){
			$this->onChanged();
		}
	}

	/**
	 * @param int $index 0-3
	 *
	 * @return string
	 */
	public function getLine(int $index) : string{
		if($index < 0 or $index > 3){
			throw new InvalidArgumentException("Index must be in the range 0-3!");
		}
		return $this->namedtag->getString("Text" . ($index + 1));
	}

	public function getText(){
		return [
			$this->namedtag->getString("Text1"),
			$this->namedtag->getString("Text2"),
			$this->namedtag->getString("Text3"),
			$this->namedtag->getString("Text4")
		];
	}

	public function addAdditionalSpawnData(CompoundTag $nbt, bool $isBedrock){
		if($isBedrock){
			$nbt->setString("Text", BedrockUtils::convertSignLinesToText($this->getText()));
		}else{
			for($i = 1; $i <= 4; $i++){
				$textKey = "Text$i";
				$nbt->setString($textKey, $this->namedtag->getString($textKey));
			}
		}
		return $nbt;
	}

	public function updateCompoundTag(CompoundTag $nbt, Player $player) : bool{
		if($nbt->getString("id") !== Tile::SIGN){
			return false;
		}

		$removeFormat = $player->getRemoveFormat();
		if($player instanceof BedrockPlayer){
			$lines = BedrockUtils::convertSignTextToLines(TextFormat::clean($nbt->getString("Text"), $removeFormat));
		}else{
			$lines = [
				TextFormat::clean($nbt->getString("Text1"), $removeFormat),
				TextFormat::clean($nbt->getString("Text2"), $removeFormat),
				TextFormat::clean($nbt->getString("Text3"), $removeFormat),
				TextFormat::clean($nbt->getString("Text4"), $removeFormat)
			];
		}
		$ev = new SignChangeEvent($this->getBlock(), $player, $lines);

		if($this->namedtag->getString("Creator", "") !== $player->getRawUniqueId()){
			$ev->setCancelled();
		}

		$ev->call();

		if(!$ev->isCancelled()){
			$this->setText(...$ev->getLines());
			return true;
		}else{
			return false;
		}
	}

}
