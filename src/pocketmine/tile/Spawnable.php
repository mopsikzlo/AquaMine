<?php

declare(strict_types=1);

namespace pocketmine\tile;

use pocketmine\BedrockPlayer;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\network\bedrock\protocol\BlockActorDataPacket as BedrockBlockActorDataPacket;
use pocketmine\network\mcpe\NetworkNbtSerializer;
use pocketmine\network\mcpe\protocol\BlockEntityDataPacket as McpeBlockEntityDataPacket;
use pocketmine\Player;

abstract class Spawnable extends Tile{
	/** @var string|null */
	private $mcpeSpawnCompoundCache = null;
	/** @var string|null */
	private $bedrockSpawnCompoundCache = null;

	public function spawnTo(Player $player){
		if($this->closed){
			return false;
		}

		$isBedrock = $player instanceof BedrockPlayer;

		$pk = $isBedrock ? new BedrockBlockActorDataPacket() : new McpeBlockEntityDataPacket();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->namedtag = $this->getSerializedSpawnCompound($isBedrock);
		$player->sendDataPacket($pk);

		return true;
	}

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->spawnToAll();
	}

	public function spawnToAll(){
		if($this->closed){
			return;
		}

		foreach($this->getLevel()->getChunkPlayers($this->chunk->getX(), $this->chunk->getZ()) as $player){
			if($player->spawned === true){
				$this->spawnTo($player);
			}
		}
	}

	/**
	 * Performs actions needed when the tile is modified, such as clearing caches and respawning the tile to players.
	 * WARNING: This MUST be called to clear spawn-compound and chunk caches when the tile's spawn compound has changed!
	 */
	protected function onChanged(){
		$this->mcpeSpawnCompoundCache = $this->bedrockSpawnCompoundCache = null;
		$this->spawnToAll();

		if($this->chunk !== null){
			$this->chunk->setChanged();
			$this->level->onTileChanged($this);
		}
	}

	/**
	 * Returns encoded NBT (varint, little-endian) used to spawn this tile to clients. Uses cache where possible,
	 * populates cache if it is null.
	 *
	 * @param bool $isBedrock
	 *
	 * @return string encoded NBT
	 */
	final public function getSerializedSpawnCompound(bool $isBedrock) : string{
		if($isBedrock){
			$cache = &$this->mcpeSpawnCompoundCache;
		}else{
			$cache = &$this->bedrockSpawnCompoundCache;
		}
		if($cache === null){
			$cache = (new NetworkNbtSerializer())->write(new TreeRoot($this->getSpawnCompound($isBedrock)));
		}

		return $cache;
	}

	/**
	 * @param bool $isBedrock
	 *
	 * @return CompoundTag
	 */
	final public function getSpawnCompound(bool $isBedrock) : CompoundTag{
		$nbt = CompoundTag::create()
			->setString("id", $this->namedtag->getString("id"))
			->setInt("x", $this->namedtag->getInt("x"))
			->setInt("y", $this->namedtag->getInt("y"))
			->setInt("z", $this->namedtag->getInt("z"));
		$this->addAdditionalSpawnData($nbt, $isBedrock);
		return $nbt;
	}

	/**
	 * An extension to getSpawnCompound() for
	 * further modifying the generic tile NBT.
	 *
	 * @param CompoundTag $nbt
	 * @param bool $isBedrock
	 */
	abstract public function addAdditionalSpawnData(CompoundTag $nbt, bool $isBedrock);

	/**
	 * Called when a player updates a block entity's NBT data
	 * for example when writing on a sign.
	 *
	 * @param CompoundTag $nbt
	 * @param Player      $player
	 *
	 * @return bool indication of success, will respawn the tile to the player if false.
	 */
	public function updateCompoundTag(CompoundTag $nbt, Player $player) : bool{
		return false;
	}
}
