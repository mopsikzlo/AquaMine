<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\skin;

use pocketmine\network\bedrock\protocol\types\skin\Skin as BedrockSkin;
use pocketmine\network\mcpe\protocol\types\Skin as McpeSkin;
use function array_rand;
use function file_get_contents;
use function json_decode;
use function substr;

class SkinConverter{

	/** @var string[][] */
	private static $defaultSkins;

	public static function convert(BedrockSkin $skin) : McpeSkin{
		$skinImage = $skin->getSkinImage();
		if($skinImage->getWidth() === 64 and ($skinImage->getHeight() === 32 or $skinImage->getHeight() === 64)){
			$skinResourcePatch = json_decode($skin->getSkinResourcePatch(), true);
			if($skinResourcePatch !== null and isset($skinResourcePatch["geometry"]["default"]) and $skinResourcePatch["geometry"]["default"] === "geometry.humanoid.customSlim"){
				$skinId = "Standard_CustomSlim";
			}else{
				$skinId = "Standard_Custom";
			}
			$skinData = $skinImage->getData();
			return new McpeSkin($skinId, $skinData);
		}else{
			self::$defaultSkins = self::$defaultSkins ?? [
				"steve" => new McpeSkin("Standard_Steve", file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/skins/steve.skindata")),
				"alex" => new McpeSkin("Standard_Alex", file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/skins/alex.skindata")),
			];

			if($skin->isPersona() and substr($skin->getSkinId(), -4, 3) === "_0_"){
				$num = substr($skin->getSkinId(), -1);
				if($num === "0"){
					$mcpeSkin = self::$defaultSkins["steve"];
				}else{
					$mcpeSkin = self::$defaultSkins["alex"];
				}
			}
			return $mcpeSkin ?? self::$defaultSkins[array_rand(self::$defaultSkins)];
		}
	}

	private function __construct(){
		// oof
	}
}