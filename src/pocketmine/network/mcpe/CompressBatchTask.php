<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

use pocketmine\network\CompressBatchPromise;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class CompressBatchTask extends AsyncTask{

	/** @var string */
	protected $data;
	/** @var int */
	protected $level = 7;

	public function __construct(string $data, int $compressionLevel, CompressBatchPromise $promise){
		$this->data = $data;
		$this->level = $compressionLevel;

		parent::__construct($promise);
	}

	public function onRun(){
		$this->setResult(NetworkCompression::compress($this->data, $this->level), false);
	}

	public function onCompletion(Server $server){
		$promise = $this->fetchLocal();

		if($promise instanceof CompressBatchPromise){
			$promise->resolve($this->getResult());
		}
	}
}
