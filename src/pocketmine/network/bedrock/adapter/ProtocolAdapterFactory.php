<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter;

use InvalidArgumentException;
use pocketmine\network\bedrock\adapter\v407\Protocol407Adapter;
use pocketmine\network\bedrock\adapter\v408\Protocol408Adapter;
use pocketmine\network\bedrock\adapter\v419\Protocol419Adapter;
use pocketmine\network\bedrock\adapter\v422\Protocol422Adapter;
use pocketmine\network\bedrock\adapter\v428\Protocol428Adapter;
use pocketmine\network\bedrock\adapter\v431\Protocol431Adapter;
use pocketmine\network\bedrock\adapter\v440\Protocol440Adapter;
use pocketmine\network\bedrock\adapter\v448\Protocol448Adapter;
use pocketmine\network\bedrock\adapter\v465\Protocol465Adapter;
use pocketmine\network\bedrock\adapter\v471\Protocol471Adapter;
use pocketmine\network\bedrock\adapter\v475\Protocol475Adapter;
use pocketmine\network\bedrock\adapter\v486\Protocol486Adapter;
use pocketmine\network\bedrock\adapter\v503\Protocol503Adapter;
use pocketmine\network\bedrock\protocol\ProtocolInfo;

final class ProtocolAdapterFactory{

	/** @var ProtocolAdapter[] */
	protected static $protocolAdapters;

	public static function lazyInit() : void{
		if(self::$protocolAdapters === null){
			self::init();
		}
	}

	public static function init() : void{
		self::$protocolAdapters = [];

		self::register(new Protocol407Adapter());
		self::register(new Protocol408Adapter());
		self::register(new Protocol419Adapter());
		self::register(new Protocol422Adapter());
		self::register(new Protocol428Adapter());
		self::register(new Protocol431Adapter());
		self::register(new Protocol440Adapter());
		self::register(new Protocol448Adapter());
		self::register(new Protocol465Adapter());
		self::register(new Protocol471Adapter());
		self::register(new Protocol475Adapter());
		self::register(new Protocol486Adapter());
		self::register(new Protocol503Adapter());
	}

	/**
	 * @param ProtocolAdapter $adapter
	 */
	public static function register(ProtocolAdapter $adapter) : void{
		if($adapter->getProtocolVersion() === ProtocolInfo::CURRENT_PROTOCOL){
			throw new InvalidArgumentException("Can't register an adapter for current protocol version");
		}
		if(isset(self::$protocolAdapters[$adapter->getProtocolVersion()])){
			throw new InvalidArgumentException("Can't override protocol adapter with version {$adapter->getProtocolVersion()}");
		}
		self::$protocolAdapters[$adapter->getProtocolVersion()] = $adapter;
	}

	/**
	 * @param int $protocolVersion
	 *
	 * @return ProtocolAdapter|null
	 */
	public static function get(int $protocolVersion) : ?ProtocolAdapter{
		return self::$protocolAdapters[$protocolVersion] ?? null;
	}

	/**
	 * @return ProtocolAdapter[]
	 */
	public static function getAll() : array{
		return self::$protocolAdapters;
	}

	private function __construct(){
		// oof
	}
}