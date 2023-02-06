<?php

namespace pocketmine\inventory;

use pocketmine\event\inventory\InventoryClickEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\Player;
use function microtime;

class SimpleTransactionQueue implements TransactionQueue{

	/** @var Player[] */
	protected $player = null;

	/** @var \SplQueue */
	protected $transactionQueue;
	/** @var \SplQueue */
	protected $transactionsToRetry;

	/** @var float */
	protected $lastUpdate = -1;

	/** @var int */
	protected $transactionCount = 0;

	/**
	 * @param Player $player
	 */
	public function __construct(Player $player = null){
		$this->player = $player;
		$this->transactionQueue = new \SplQueue();
		$this->transactionsToRetry = new \SplQueue();
	}

	/**
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}

	public function getTransactionCount(){
		return $this->transactionCount;
	}

	/**
	 * @return \SplQueue
	 */
	public function getTransactions(){
		return $this->transactionQueue;
	}

	/**
	 * @param Transaction $transaction
	 *
	 * Adds a transaction to the queue
	 */
	public function addTransaction(Transaction $transaction){
		$this->transactionQueue->enqueue($transaction);
		$this->lastUpdate = microtime(true);
		$this->transactionCount += 1;
	}

	/**
	 * Handles transaction queue execution
	 */
	public function execute(){
		/** @var Transaction[] */
		$failed = [];

		while(!$this->transactionsToRetry->isEmpty()){
			//Some failed transactions are waiting from the previous execution to be retried
			$this->transactionQueue->enqueue($this->transactionsToRetry->dequeue());
		}

		if(!$this->transactionQueue->isEmpty()){
 			$ev = new InventoryTransactionEvent($this);
 			$ev->call();
 		}else{
 			return;
 		}

		while(!$this->transactionQueue->isEmpty()){

			$transaction = $this->transactionQueue->dequeue();

			if($transaction instanceof BaseTransaction){
				if($transaction->getInventory() instanceof ContainerInventory or $transaction->getInventory() instanceof PlayerInventory){
					$event = new InventoryClickEvent($transaction->getInventory(), $this->player, $transaction->getSlot(), $transaction->getInventory()->getItem($transaction->getSlot()));
					$event->setCancelled($ev->isCancelled());
					$event->call();

					if($event->isCancelled()){
						$ev->setCancelled();
					}
				}
			}elseif($transaction instanceof SwapTransaction){
				if($transaction->getFirstInventory() instanceof ContainerInventory or $transaction->getFirstInventory() instanceof PlayerInventory){
					$event = new InventoryClickEvent($transaction->getFirstInventory(), $this->player, $transaction->getFirstSlot(), $transaction->getFirstInventory()->getItem($transaction->getFirstSlot()));
					$event->setCancelled($ev->isCancelled());
					$event->call();

					if($event->isCancelled()){
						$ev->setCancelled();
					}
				}
				if($transaction->getSecondInventory() instanceof ContainerInventory or $transaction->getSecondInventory() instanceof PlayerInventory){
					$event = new InventoryClickEvent($transaction->getSecondInventory(), $this->player, $transaction->getSecondSlot(), $transaction->getSecondInventory()->getItem($transaction->getSecondSlot()));
					$event->setCancelled($ev->isCancelled());
					$event->call();

					if($event->isCancelled()){
						$ev->setCancelled();
					}
				}
			}

			if($ev->isCancelled()){
				$transaction->sendSlotUpdate($this->player); //Send update back to client for cancelled transaction
				continue;
			}elseif(!$transaction->execute($this->player)){
				$transaction->addFailure();
				if($transaction->getFailures() >= self::DEFAULT_ALLOWED_RETRIES){
					/* Transaction failed completely after several retries, hold onto it to send a slot update */
					$this->transactionCount -= 1;
					$failed[] = $transaction;
				}else{
					/* Add the transaction to the back of the queue to be retried on the next tick */
					$this->transactionsToRetry->enqueue($transaction);
				}
				continue;
			}

			$this->transactionCount -= 1;
			$transaction->setSuccess();
			$transaction->sendSlotUpdate($this->player);
		}

		foreach($failed as $f){
			$f->sendSlotUpdate($this->player);
		}

		return true;
	}
}