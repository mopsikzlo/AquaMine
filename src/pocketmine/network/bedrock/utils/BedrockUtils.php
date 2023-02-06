<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\utils;

use pocketmine\BedrockPlayer;

final class BedrockUtils{

	public static function splitPlayers(array $players, &$pw10Players, &$bedrockPlayers) : void{
		$pw10Players = [];
		$bedrockPlayers = [];

		foreach($players as $player){
			if($player instanceof BedrockPlayer){
				$bedrockPlayers[] = $player;
			}else{
				$pw10Players[] = $player;
			}
		}
	}

	/**
	 * @param string $text
	 *
	 * @return string[]
	 */
	public static function convertSignTextToLines(string $text) : array{
		return array_slice(array_pad(explode("\n", $text), 4, ""), 0, 4);
	}

	/**
	 * @param string[] $lines
	 *
	 * @return string
	 */
	public static function convertSignLinesToText(array $lines) : string{
		return implode("\n", $lines);
	}

	private function __construct(){
		// oof
	}
}