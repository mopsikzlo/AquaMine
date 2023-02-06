<?php

declare(strict_types=1);

namespace pocketmine\event;

class TextContainer{

	/** @var string $text */
	protected $text;

	/**
	 * @param string $text
	 */
	public function __construct(string $text){
		$this->text = $text;
	}

	/**
	 * @param string $text
	 */
	public function setText(string $text){
		$this->text = $text;
	}

	/**
	 * @return string
	 */
	public function getText() : string{
		return $this->text;
	}

	/**
	 * @return string
	 */
	public function __toString() : string{
		return $this->getText();
	}
}