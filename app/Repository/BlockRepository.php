<?php

namespace App\Repository;

use App\Crypto\BlockHasher;
use App\Crypto\TransactionHasher;
use App\Crypto\TransactionSerializer;
use App\Node\Balance;
use App\Node\BalanceFactory;
use App\Node\Difficulty;
use App\NodeBalance;
use App\NodeBlock;
use App\NodeTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BlockRepository
{
    /**
     * @var Difficulty
     */
    private $difficulty;
    /**
     * @var BalanceFactory
     */
    private $balanceFactory;
    
    /**
     * BlockRepository constructor.
     * @param Difficulty $difficulty
     * @param BalanceFactory $balanceFactory
     */
    public function __construct(Difficulty $difficulty, BalanceFactory $balanceFactory)
    {
        $this->difficulty = $difficulty;
        $this->balanceFactory = $balanceFactory;
    }
    
    /**
     * @return NodeBlock
     */
    public function getTopBlock(): NodeBlock
    {
        return NodeBlock::query()->orderBy('index', 'desc')->firstOrFail();
    }
    
    /**
     * @return Collection|NodeBlock[]
     */
    public function getAllBlocks()
    {
        return NodeBlock::query()->orderBy('index', 'asc')->get();
    }
    
    public function newGenesisBlock($initial_funds = [], $timestamp = 1529067174): NodeBlock
    {
        $genesis = new NodeBlock([
                'index'                => "0",
                'difficulty'           => "0",
                'cumulativeDifficulty' => "0",
                'nonce'                => "0",
                'mined_by_address'     => str_repeat('0', 40),
                'previous_block_hash'  => str_repeat('0', 64),
                'timestamp'            => $timestamp,
        ]);
    
        $transactionHasher = new TransactionHasher(new TransactionSerializer());
        foreach ($initial_funds as $address => $value) {
            $transaction = new NodeTransaction();
            $transaction->timestamp = $timestamp;
            $transaction->fee = "0";
            $transaction->value = $value;
            $transaction->senderAddress = str_repeat('0', 40);
            $transaction->senderSequence = "0";
            $transaction->data = '';
            $transaction->receiverAddress = $address;
            $transaction->hash = $transactionHasher->getHash($transaction);
            $transaction->signature = str_repeat(0, 130);
            $genesis->transactions[] = $transaction;
        }
    
        $hasher = new BlockHasher();
    
        $genesis->data_hash = $hasher->getDataHash($genesis);
    
        $genesis->block_hash = $hasher->getBlockHash($genesis);
    
        return $genesis;
    }
    /**
     * @param array $initial_funds [address => value]
     * @param int $timestamp
     * @return NodeBlock
     * @throws \Exception
     */
    public function getGenesisBlock(): NodeBlock
    {
        return NodeBlock::where('index', '=', 0)->firstOrFail();
    }
    
    public function getBlockWithHash($block_hash)
    {
        return NodeBlock::where('block_hash', '=', $block_hash)->first();
    }
    
    public function updateWithChain($chain)
    {
        DB::transaction(function() use ($chain){
            $updatedChain = $this->difference($chain);
            $base = $updatedChain[0];
            // Delete all blocks after the base's parent hash, making all transactions after that pending (temporarily)
            NodeBlock::where('index', '>=', $base->index)->delete();
    
            /**
             * @var Balance $balance;
             */
            $balance = null;
            foreach ($updatedChain as $i => $updatedBlock) {
                if ($i == 0){
                    $parent = $this->getBlockWithHash($updatedBlock->previous_block_hash);
                    if (!$parent){
                        throw new \Exception('Could not find parent of the update chain');
                    }
                    $balance = $this->balanceFactory->forBlock($parent);
                } else {
                    $parent = $updatedChain[$i-1];
                }
                
                // Update the difficulty
                $updatedBlock->cumulativeDifficulty = $parent->cumulativeDifficulty + $this->difficulty->difficultyOfBlock($updatedBlock);
                
                
        
                $updatedBlock->save();
                
                $balance->updateForBlock($updatedBlock); // throws
                
                
                $this->linkTransactions($updatedBlock);
        
            }
        });
        
        
    }
    
    /**
     * @param NodeBlock $block
     * @param $sequence
     * @param $handleMissingTransactions
     */
    public function linkTransactions(
            $block
    ): void {
        $sequence = 0;
// Link up all transactions based on the hashes to the current block! $update->transactions
        foreach ($block->transactions as $transaction) {
            $existingTransaction = NodeTransaction::where('hash', '=', $transaction->hash)->whereNull('block_id')->first();
            if ($existingTransaction) {
               
                $existingTransaction->sequence = $sequence++;
                $existingTransaction->block_id = $block->id;
                $existingTransaction->save();
            } else {
                $newTransaction = $transaction;
                $newTransaction->sequence = $sequence++;
                $newTransaction->block_id = $block->id;
                $newTransaction->save();
            }
        }
    }
    
    /**
     * Get the part of the chain that is different then the current active chain
     * @param NodeBlock[] $chain
     * @return NodeBlock[]
     */
    private function difference($chain)
    {
        foreach ($chain as $i => $block){
            if (!$this->getBlockWithHash($block->block_hash)){
               // missing hash!
                return array_slice($chain, $i);
            }
        }
        return []; // The fully exists
    }
    
    /**
     * @param $index
     * @return NodeBalance|null
     */
    public function getBlockWithIndex($index)
    {
        return NodeBlock::where('index', '=', $index)->first();
    }
}
