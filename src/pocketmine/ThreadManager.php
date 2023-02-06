<?php

declare(strict_types=1);

namespace pocketmine;

use function spl_object_id;

class ThreadManager extends \Volatile{

	/** @var ThreadManager */
	private static $instance = null;

	public static function init(){
		self::$instance = new ThreadManager();
	}

	/**
	 * @return ThreadManager
	 */
	public static function getInstance(){
		return self::$instance;
	}

	/**
	 * @param Worker|Thread $thread
	 */
	public function add($thread){
		if($thread instanceof Thread or $thread instanceof Worker){
			$this->{spl_object_id($thread)} = $thread;
		}
	}

	/**
	 * @param Worker|Thread $thread
	 */
	public function remove($thread){
		if($thread instanceof Thread or $thread instanceof Worker){
			unset($this->{spl_object_id($thread)});
		}
	}

	/**
	 * @return Worker[]|Thread[]
	 */
	public function getAll() : array{
		$array = [];
		foreach($this as $key => $thread){
			$array[$key] = $thread;
		}

		return $array;
	}
}