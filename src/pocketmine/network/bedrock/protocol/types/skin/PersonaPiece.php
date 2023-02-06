<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\skin;

class PersonaPiece{

	/** @var string */
	protected $pieceId;
	/** @var string */
	protected $pieceType;
	/** @var string */
	protected $packId;
	/** @var bool */
	protected $isDefault;
	/** @var string */
	protected $productId;

	public function __construct(string $pieceId, string $pieceType, string $packId, bool $isDefaultPiece, string $productId){
		$this->pieceId = $pieceId;
		$this->pieceType = $pieceType;
		$this->packId = $packId;
		$this->isDefault = $isDefaultPiece;
		$this->productId = $productId;
	}

	/**
	 * @return string
	 */
	public function getPieceId() : string{
		return $this->pieceId;
	}

	/**
	 * @return string
	 */
	public function getPieceType() : string{
		return $this->pieceType;
	}

	/**
	 * @return string
	 */
	public function getPackId() : string{
		return $this->packId;
	}

	/**
	 * @return bool
	 */
	public function isDefault() : bool{
		return $this->isDefault;
	}

	/**
	 * @return string
	 */
	public function getProductId() : string{
		return $this->productId;
	}
}