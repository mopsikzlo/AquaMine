<?php

declare(strict_types=1);

namespace pocketmine\command;

use pocketmine\plugin\Plugin;

interface PluginIdentifiableCommand{

	/**
	 * @return Plugin
	 */
	public function getPlugin() : Plugin;
}
