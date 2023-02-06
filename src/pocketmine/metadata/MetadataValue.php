<?php

declare(strict_types=1);

namespace pocketmine\metadata;

use pocketmine\plugin\Plugin;

abstract class MetadataValue{
	/** @var Plugin */
	private $owningPlugin;

	protected function __construct(Plugin $owningPlugin){
		$this->owningPlugin = $owningPlugin;
	}

	/**
	 * @return Plugin
	 */
	public function getOwningPlugin(){
		return $this->owningPlugin;
	}

	/**
	 * Fetches the value of this metadata item.
	 *
	 * @return mixed
	 */
	abstract public function value();

	/**
	 * Invalidates this metadata item, forcing it to recompute when next
	 * accessed.
	 */
	abstract public function invalidate();
}