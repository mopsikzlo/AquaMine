<?php

declare(strict_types=1);

namespace pocketmine\permission;


interface PermissionRemovedExecutor{

	/**
	 * @param PermissionAttachment $attachment
	 *
	 * @return void
	 */
	public function attachmentRemoved(PermissionAttachment $attachment);
}