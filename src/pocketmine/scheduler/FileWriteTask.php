<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

use function file_put_contents;

class FileWriteTask extends AsyncTask{

	/** @var string */
	private $path;
	/** @var mixed */
	private $contents;
	/** @var int */
	private $flags;

	/**
	 * @param string $path
	 * @param mixed  $contents
	 * @param int    $flags
	 */
	public function __construct(string $path, $contents, int $flags = 0){
		$this->path = $path;
		$this->contents = $contents;
		$this->flags = $flags;
	}

	public function onRun(){
		try{
			file_put_contents($this->path, $this->contents, $this->flags);
		}catch(\Throwable $e){

		}
	}
}
