<?php

/*
 * PocketMine Standard PHP Library
 * Copyright (C) 2014-2017 PocketMine Team <https://github.com/PocketMine/PocketMine-SPL>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
*/

interface LogLevel{
	public const EMERGENCY = "emergency";
	public const ALERT = "alert";
	public const CRITICAL = "critical";
	public const ERROR = "error";
	public const WARNING = "warning";
	public const NOTICE = "notice";
	public const INFO = "info";
	public const DEBUG = "debug";
}