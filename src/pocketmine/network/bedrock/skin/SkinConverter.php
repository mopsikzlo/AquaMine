<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\skin;

use pocketmine\network\bedrock\protocol\types\skin\SerializedSkinImage;
use pocketmine\network\bedrock\protocol\types\skin\Skin as BedrockSkin;
use pocketmine\network\mcpe\protocol\types\Skin as McpeSkin;
use function file_get_contents;
use function json_decode;

class SkinConverter{

	/** @var array[] */
	private static $skinIdToBedrockMap = [];

	public static function init() : void{
		$skinIdMap = json_decode(file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/pw10_skins/skin_id_map.json"), true);
		foreach($skinIdMap as $skinId => $item){
			$geometryData = file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/pw10_skins/geometry/" . $item["geometry"] . ".json");

			$data = [
				"geometry" => $item["geometry"],
				"geometryData" => $geometryData,
			];
			if(isset($item["cape"])){
				$capeData = file_get_contents(\pocketmine\PATH . "src/pocketmine/resources/bedrock/pw10_skins/capes/" . $item["cape"] . ".skindata");

				$data["cape"] = $item["cape"];
				$data["capeData"] = $capeData;
			}
			self::$skinIdToBedrockMap[$skinId] = $data;
		}
	}

	public static function convert(McpeSkin $mcpeSkin) : BedrockSkin{
		$mapping = self::$skinIdToBedrockMap[$mcpeSkin->getSkinId()] ?? null;
		if($mapping === null){
			return BedrockSkin::empty();
		}

		return new BedrockSkin(
			$mcpeSkin->getSkinId(),
			"",
			'{"geometry":{"default":"' . $mapping["geometry"] . '"}}',
			SerializedSkinImage::fromLegacyImageData($mcpeSkin->getSkinData()),
			[],
			SerializedSkinImage::fromLegacyImageData($mapping["capeData"] ?? ""),
			$mapping["geometryData"],
			"",
			false,
			false,
			false,
			$mapping["cape"] ?? ""
		);
	}
}