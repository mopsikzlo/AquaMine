<?php

declare(strict_types=1);

namespace pocketmine\plugin;


abstract class PluginLoadOrder{
	/*
	 * The plugin will be loaded before the other plugins
	 */
	public const PRESTARTUP = -1;

	/*
	 * The plugin will be loaded at startup
	 */
	public const STARTUP = 0;

	/*
	 * The plugin will be loaded after the first world has been loaded/created.
	 */
	public const POSTWORLD = 1;
}