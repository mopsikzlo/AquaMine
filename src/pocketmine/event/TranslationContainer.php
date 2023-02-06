<?php

declare(strict_types=1);

namespace pocketmine\event;

use function count;

class TranslationContainer extends TextContainer{

	/** @var string[] $params */
	protected $params = [];

	/**
	 * @param string   $text
	 * @param string[] $params
	 */
	public function __construct(string $text, array $params = []){
		parent::__construct($text);

		$this->setParameters($params);
	}

	/**
	 * @return string[]
	 */
	public function getParameters() : array{
		return $this->params;
	}

	/**
	 * @param int $i
	 *
	 * @return string|null
	 */
	public function getParameter(int $i){
		return $this->params[$i] ?? null;
	}

	/**
	 * @param int    $i
	 * @param string $str
	 */
	public function setParameter(int $i, string $str){
		if($i < 0 or $i > count($this->params)){ //Intended, allow to set the last
			throw new \InvalidArgumentException("Invalid index $i, have " . count($this->params));
		}

		$this->params[$i] = $str;
	}

	/**
	 * @param string[] $params
	 */
	public function setParameters(array $params){
		$i = 0;
		foreach($params as $str){
			$this->params[$i] = (string) $str;

			++$i;
		}
	}
}