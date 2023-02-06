<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

use pocketmine\utils\Internet;

/**
 * Executes a consecutive list of cURL operations.
 *
 * The result of this AsyncTask is an array of arrays (returned from {@link Internet::simpleCurl}) or RuntimeException objects.
 *
 * @package pocketmine\scheduler
 */
use function igbinary_serialize;
use function igbinary_unserialize;

class BulkCurlTask extends AsyncTask{
	private $operations;

	/**
	 * BulkCurlTask constructor.
	 *
	 * $operations accepts an array of arrays. Each member array must contain a string mapped to "page", and optionally,
	 * "timeout", "extraHeaders" and "extraOpts". Documentation of these options are same as those in
	 * {@link Internet::simpleCurl}.
	 *
	 * @param array      $operations
	 * @param mixed|null $complexData
	 */
	public function __construct(array $operations, $complexData = null){
		parent::__construct($complexData);
		$this->operations = igbinary_serialize($operations);
	}

	public function onRun(){
		$operations = igbinary_unserialize($this->operations);
		$results = [];
		foreach($operations as $op){
			try{
				$results[] = Internet::simpleCurl($op["page"], $op["timeout"] ?? 10, $op["extraHeaders"] ?? [], $op["extraOpts"] ?? []);
			}catch(\RuntimeException $e){
				$results[] = $e;
			}
		}
		$this->setResult($results);
	}
}
