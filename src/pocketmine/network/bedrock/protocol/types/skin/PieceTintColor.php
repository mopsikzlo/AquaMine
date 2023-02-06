<?php

declare(strict_types=1);

namespace pocketmine\network\bedrock\protocol\types\skin;

class PieceTintColor{

	/** @var string */
	protected $pieceType;
	/** @var string[] */
	protected $colors;

	/**
	 * @param string $pieceType
	 * @param string[] $colors
	 */
	public function __construct(string $pieceType, array $colors){
		$this->pieceType = $pieceType;
		$this->colors = $colors;
	}

	/**
	 * @return string
	 */
	public function getPieceType() : string{
		return $this->pieceType;
	}

	/**
	 * @return string[]
	 */
	public function getColors() : array{
		return $this->colors;
	}
}