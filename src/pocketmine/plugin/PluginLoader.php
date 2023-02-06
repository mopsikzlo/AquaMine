<?php

declare(strict_types=1);

namespace pocketmine\plugin;

/**
 * Handles different types of plugins
 */
interface PluginLoader{

	/**
	 * Loads the plugin contained in $file
	 *
	 * @param string $file
	 *
	 * @return Plugin|null
	 */
	public function loadPlugin(string $file);

	/**
	 * Gets the PluginDescription from the file
	 *
	 * @param string $file
	 *
	 * @return null|PluginDescription
	 */
	public function getPluginDescription(string $file);

	/**
	 * Returns the filename regex patterns that this loader accepts
	 *
	 * @return string
	 */
	public function getPluginFilters() : string;

	/**
	 * @param Plugin $plugin
	 *
	 * @return void
	 */
	public function enablePlugin(Plugin $plugin);

	/**
	 * @param Plugin $plugin
	 *
	 * @return void
	 */
	public function disablePlugin(Plugin $plugin);


}