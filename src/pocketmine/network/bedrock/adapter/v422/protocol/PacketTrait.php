<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\adapter\v422\protocol;

#include <rules/DataPacket.h>


use pocketmine\nbt\TreeRoot;
use pocketmine\network\bedrock\protocol\types\actor\ActorMetadataProperties;
use pocketmine\network\bedrock\adapter\v422\protocol\types\ActorMetadataProperties as ActorMetadataProperties422;
use pocketmine\network\bedrock\protocol\types\actor\ActorMetadataTypes;
use pocketmine\network\bedrock\protocol\types\skin\Skin;
use pocketmine\network\bedrock\protocol\types\skin\SkinAnimation;
use pocketmine\network\mcpe\NetworkNbtSerializer;
use UnexpectedValueException;
use function count;

trait PacketTrait{

	/**
	 * @return Skin
	 */
	public function getSkin() : Skin{
		$skinId = $this->getString();
		$skinResourcePatch = $this->getString();
		$skinImage = $this->getImage();

		$animations = [];
		$count = $this->getLInt();
		if($count > 128){
			throw new UnexpectedValueException("Too many skin animations: $count");
		}
		for($i = 0; $i < $count; ++$i){
			$image = $this->getImage();
			$type = $this->getLInt();
			$frames = $this->getLFloat();
			$expressionType = $this->getLInt();
			$animations[] = new SkinAnimation($image, $type, $frames, $expressionType);
		}

		$capeImage = $this->getImage();
		$geometryData = $this->getString();
		$animationData = $this->getString();
		$isPremium = $this->getBool();
		$isPersona = $this->getBool();
		$isCapeOnClassic = $this->getBool();
		$capeId = $this->getString();
		$fullSkinId = $this->getString();
		$armSize = $this->getString();
		$skinColor = $this->getString();

		$personaPieces = [];
		$count = $this->getLInt();
		if($count > 128){
			throw new UnexpectedValueException("Too many persona pieces: $count");
		}
		for($i = 0; $i < $count; ++$i){
			$personaPieces[] = $this->getPersonaPiece();
		}

		$pieceTintColors = [];
		$count = $this->getLInt();
		if($count > 128){
			throw new UnexpectedValueException("Too many piece tint colors: $count");
		}
		for($i = 0; $i < $count; ++$i){
			$pieceTintColors[] = $this->getPieceTintColor();
		}

		return new Skin($skinId, "", $skinResourcePatch, $skinImage, $animations, $capeImage, $geometryData, $animationData, $isPremium, $isPersona, $isCapeOnClassic, $capeId, $fullSkinId, $armSize, $skinColor, $personaPieces, $pieceTintColors);
	}

	/**
	 * @param Skin $skin
	 */
	public function putSkin(Skin $skin) : void{
		$this->putString($skin->getSkinId());
		$this->putString($skin->getSkinResourcePatch());
		$this->putImage($skin->getSkinImage());

		$animations = $skin->getAnimations();
		$this->putLInt(count($animations));
		foreach($animations as $animation){
			$this->putImage($animation->getImage());
			$this->putLInt($animation->getType());
			$this->putLFloat($animation->getFrames());
			$this->putLInt($animation->getExpressionType());
		}

		$this->putImage($skin->getCapeImage());
		$this->putString($skin->getGeometryData());
		$this->putString($skin->getAnimationData());
		$this->putBool($skin->isPremium());
		$this->putBool($skin->isPersona());
		$this->putBool($skin->isCapeOnClassic());
		$this->putString($skin->getCapeId());
		$this->putString($skin->getFullSkinId());
		$this->putString($skin->getArmSize());
		$this->putString($skin->getSkinColor());

		$this->putLInt(count($skin->getPersonaPieces()));
		foreach($skin->getPersonaPieces() as $personaPiece){
			$this->putPersonaPiece($personaPiece);
		}

		$this->putLInt(count($skin->getPieceTintColors()));
		foreach($skin->getPieceTintColors() as $pieceTintColor){
			$this->putPieceTintColor($pieceTintColor);
		}
	}

	/**
	 * Decodes actor metadata from the stream.
	 *
	 * @param bool $types Whether to include metadata types along with values in the returned array
	 *
	 * @return array
	 */
	public function getActorMetadata(bool $types = true) : array{
		$count = $this->getUnsignedVarInt();
		if($count > 128){
			throw new UnexpectedValueException("Too many actor metadata: $count");
		}
		$data = [];
		for($i = 0; $i < $count; ++$i){
			$key = $this->getUnsignedVarInt();
			if($key >= ActorMetadataProperties422::AREA_EFFECT_CLOUD_RADIUS){
				++$key;
			}
			$type = $this->getUnsignedVarInt();
			$value = null;
			switch($type){
				case ActorMetadataTypes::BYTE:
					$value = $this->getByte();
					break;
				case ActorMetadataTypes::SHORT:
					$value = $this->getSignedLShort();
					break;
				case ActorMetadataTypes::INT:
					$value = $this->getVarInt();
					break;
				case ActorMetadataTypes::FLOAT:
					$value = $this->getLFloat();
					break;
				case ActorMetadataTypes::STRING:
					$value = $this->getString();
					break;
				case ActorMetadataTypes::NBT:
					$value = $this->getNbtCompoundRoot();
					break;
				case ActorMetadataTypes::POS:
					$this->getSignedBlockPosition($x, $y, $z);
					$value = [$x, $y, $z];
					break;
				case ActorMetadataTypes::LONG:
					$value = $this->getVarLong();
					break;
				case ActorMetadataTypes::VECTOR3F:
					$this->getVector3f($x, $y, $z);
					$value = [$x, $y, $z];
					break;
				default:
					throw new UnexpectedValueException("Invalid data type " . $type);
			}
			if($types){
				$data[$key] = [$type, $value];
			}else{
				$data[$key] = $value;
			}
		}

		return $data;
	}

	/**
	 * Writes actor metadata to the packet buffer.
	 *
	 * @param array $metadata
	 */
	public function putActorMetadata(array $metadata) : void{
		$this->putUnsignedVarInt(count($metadata));
		foreach($metadata as $key => $d){
			if($key >= ActorMetadataProperties::AREA_EFFECT_CLOUD_RADIUS){
				--$key;
			}
			$this->putUnsignedVarInt($key); //data key
			$this->putUnsignedVarInt($d[0]); //data type
			switch($d[0]){
				case ActorMetadataTypes::BYTE:
					$this->putByte($d[1]);
					break;
				case ActorMetadataTypes::SHORT:
					$this->putLShort($d[1]); //SIGNED short!
					break;
				case ActorMetadataTypes::INT:
					$this->putVarInt($d[1]);
					break;
				case ActorMetadataTypes::FLOAT:
					$this->putLFloat($d[1]);
					break;
				case ActorMetadataTypes::STRING:
					$this->putString($d[1]);
					break;
				case ActorMetadataTypes::NBT:
					$this->put((new NetworkNbtSerializer())->write(new TreeRoot($d[1])));
					break;
				case ActorMetadataTypes::POS:
					$this->putSignedBlockPosition(...$d[1]);
					break;
				case ActorMetadataTypes::LONG:
					$this->putVarLong($d[1]);
					break;
				case ActorMetadataTypes::VECTOR3F:
					$this->putVector3f(...$d[1]); //x, y, z
					break;
				default:
					throw new UnexpectedValueException("Invalid data type " . $d[0]);
			}
		}
	}
}