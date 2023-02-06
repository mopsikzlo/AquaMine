<?php

declare(strict_types=1);

namespace pocketmine\permission;


class PermissionAttachmentInfo{
	/** @var Permissible */
	private $permissible;

	/** @var string */
	private $permission;

	/** @var PermissionAttachment|null */
	private $attachment;

	/** @var bool */
	private $value;

	/**
	 * @param Permissible               $permissible
	 * @param string                    $permission
	 * @param PermissionAttachment|null $attachment
	 * @param bool                      $value
	 *
	 * @throws \InvalidStateException
	 */
	public function __construct(Permissible $permissible, string $permission, PermissionAttachment $attachment = null, bool $value){
		$this->permissible = $permissible;
		$this->permission = $permission;
		$this->attachment = $attachment;
		$this->value = $value;
	}

	/**
	 * @return Permissible
	 */
	public function getPermissible() : Permissible{
		return $this->permissible;
	}

	/**
	 * @return string
	 */
	public function getPermission() : string{
		return $this->permission;
	}

	/**
	 * @return PermissionAttachment|null
	 */
	public function getAttachment(){
		return $this->attachment;
	}

	/**
	 * @return bool
	 */
	public function getValue() : bool{
		return $this->value;
	}
}