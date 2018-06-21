<?php

namespace App\Node;

use App\Exceptions\InvalidTransaction;
use App\NodeBlock;
use App\NodeTransaction;
use App\Repository\BalanceRepository;

class Balance
{
    /**
     * @var array
     */
    private $balance;
    /**
     * @var BalanceRepository
     */
    private $repository;
    
    /**
     * Balance constructor.
     * @param array $balance
     */
    public function __construct($balance, BalanceRepository $repository)
    {
    
        $this->balance = $balance;
        $this->repository = $repository;
    }
    
    public function addTransaction(NodeTransaction $transaction): bool
    {
        
        if ((@$this->balance[$transaction->senderAddress] < ($transaction->value + $transaction->fee)) && !$transaction->isCoinbase) {
            throw new InvalidTransaction('Not enough funds to carry out the transaction - '. $this->balance[$transaction->senderAddress]);
        }
    
        @$this->balance[$transaction->senderAddress] -= $transaction->value + $transaction->fee;
        @$this->balance[$transaction->receiverAddress] += $transaction->value;
        // Fee is left for the miner's coinbase
        return true;
    }
    
    /**
     * @param NodeBlock $block
     * @throws InvalidTransaction
     */
    public function addBlock(NodeBlock $block): void
    {
        foreach ($block->transactions as $transaction){
            $this->addTransaction($transaction);
        }
    }
    
    public function saveForBlock(NodeBlock $block): void
    {
        if (!$block->id){
            throw new \Exception('The block needs to be persisted in the database');
        }
        
        $this->repository->saveBalancesForBlock($block, $this->balance);
        
    }
    
    /**
     * @param NodeBlock $block
     * @throws InvalidTransaction
     * @throws \Exception
     */
    public function updateForBlock(NodeBlock $block){
        $this->addBlock($block);
        $this->saveForBlock($block);
    }
    
    public function savePending(): void
    {
        $this->repository->saveBalancesForPending($this->balance);
    }
    
    public function getForAddress($address){
        return @$this->balance[$address] ?: 0;
    }
    
}