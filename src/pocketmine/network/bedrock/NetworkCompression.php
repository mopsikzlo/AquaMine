<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock;

use pocketmine\utils\Zlib;
use const ZLIB_ENCODING_RAW;

final class NetworkCompression{
	public static $LEVEL = 7;
	public static $THRESHOLD = 256;

	private function __construct(){

	}

	public static function decompress(string $payload) : string{
		return Zlib::decompress($payload, 1024 * 1024 * 2); //Max 2 MB
	}

	/**
	 * @param string $payload
	 * @param int|null $compressionLevel
	 *
	 * @return string
	 */
	public static function compress(string $payload, ?int $compressionLevel = null) : string{
		return Zlib::compress($payload, ZLIB_ENCODING_RAW, $compressionLevel ?? self::$LEVEL);
	}
}