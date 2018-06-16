<?php

namespace App\Repository;

use App\Crypto\BlockHasher;
use App\Node\Difficulty;
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
     * BlockRepository constructor.
     * @param Difficulty $difficulty
     */
    public function __construct(Difficulty $difficulty)
    {
        $this->difficulty = $difficulty;
    }
    
    /**
     * @return NodeBlock
     */
    public function getTopBlock(): NodeBlock
    {
        return NodeBlock::orderBy('index', 'desc')->firstOrFail();
    }
    
    /**
     * @return Collection|NodeBlock[]
     */
    public function getAllBlocks()
    {
        return NodeBlock::orderBy('index', 'asc')->get();
    }
    
    /**
     * @return NodeBlock
     * @throws \Exception
     */
    public function getGenesisBlock(): NodeBlock
    {
        $genesis = new NodeBlock([
                'index'               => 0,
                'difficulty'          => 0,
                'cumulativeDifficulty'=> 0,
                'nonce'               => 0,
                'mined_by_address'    => str_repeat('0', 40),
                'previous_block_hash' => str_repeat('0', 64),
                'timestamp'           => '1529067174',
        ]);
    
        $hasher = new BlockHasher();
    
        $genesis->data_hash = $hasher->getDataHash($genesis);
    
        $genesis->block_hash = $hasher->getBlockHash($genesis);
    
        return $genesis;
    }
    
    public function getBlockWithHash($block_hash)
    {
        return NodeBlock::where('block_hash', '=', $block_hash)->first();
    }
    
    public function updateWithChain($chain, $handleMissingTransactions=null)
    {
        DB::transaction(function() use ($chain, $handleMissingTransactions){
            $updatedChain = $this->difference($chain);
            $base = $updatedChain[0];
            // Delete all blocks after the base's parent hash, making all transactions after that pending (temporarily)
            NodeBlock::where('index', '>=', $base->index)->delete();
            
    
            foreach ($updatedChain as $i => $update) {
                if ($i == 0){
                    $parent = $this->getBlockWithHash($update->previous_block_hash);
                    if (!$parent){
                        throw new \Exception('Could not find parent of the update chain');
                    }
                } else {
                    $parent = $updatedChain[$i-1];
                }
                $update->cumulativeDifficulty = $parent->cumulativeDifficulty + $this->difficulty->difficultyOfBlock($update);
        
                $update->save();
                $this->linkTransactions($update, $handleMissingTransactions);
        
            }
        });
        
        
    }
    
    /**
     * @param $block
     * @param $sequence
     * @param $handleMissingTransactions
     */
    public function linkTransactions(
            $block,
            $handleMissingTransactions
    ): void {
        $sequence = 0;
// Link up all transactions based on the hashes to the current block! $update->transactions
        foreach ($block->transactionHashes as $transactionHash) {
            $existingTransaction = NodeTransaction::where('hash', '=', $transactionHash)->first();
            if ($existingTransaction) {
                $existingTransaction->sequence = $sequence++;
                $existingTransaction->block_id = $block->id;
            } else {
                if ($handleMissingTransactions) {
                    call_user_func($handleMissingTransactions, $transactionHash, $sequence, $block);
                }
            }
        }
    }
    
    public function blockCanBeAppendedTo(NodeBlock $block, NodeBlock $top)
    {
        return $block->previous_block_hash == $top->block_hash;
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
}
