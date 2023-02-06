<?php

declare(strict_types=1);

namespace pocketmine\permission;

use pocketmine\plugin\Plugin;

interface Permissible extends ServerOperator{

	/**
	 * Checks if this instance has a permission overridden
	 *
	 * @param string|Permission $name
	 *
	 * @return bool
	 */
	public function isPermissionSet($name) : bool;

	/**
	 * Returns the permission value if overridden, or the default value if not
	 *
	 * @param string|Permission $name
	 *
	 * @return bool
	 */
	public function hasPermission($name) : bool;

	/**
	 * @param Plugin $plugin
	 * @param string $name
	 * @param bool   $value
	 *
	 * @return PermissionAttachment
	 */
	public function addAttachment(Plugin $plugin, string $name = null, bool $value = null) : PermissionAttachment;

	/**
	 * @param PermissionAttachment $attachment
	 *
	 * @return void
	 */
	public function removeAttachment(PermissionAttachment $attachment);


	/**
	 * @return void
	 */
	public function recalculatePermissions();

	/**
	 * @return PermissionAttachmentInfo[]
	 */
	public function getEffectivePermissions() : array;

}