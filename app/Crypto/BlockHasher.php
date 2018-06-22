<?php

namespace App\Crypto;

use App\NodeBlock;
use App\NodeTransaction;
use PhpParser\Node;

class BlockHasher
{
    
    /**
     * HashTransaction constructor.
     * @param NodeTransaction $transaction
     */
    public function __construct()
    {
    }
    
    public function getDataHash(NodeBlock $block){
        return hash('sha256', $this->serializedBlock($block));
    }
    
    public function getBlockHash(NodeBlock $block){
        if (!isset($block->data_hash)){
            throw new \Exception('Data hash must be computed before getting the block hash!');
        }
        
        return hash('sha256', $block->data_hash.$block->timestamp.$block->nonce);
    }
    
    public function updateHashes(NodeBlock $block)
    {
        $block->data_hash = $this->getDataHash($block);
        $block->block_hash = $this->getBlockHash($block);
    }
    
    private function serializedBlock(NodeBlock $block)
    {
        $blockRepresentation = [];
        $blockRepresentation['previous_block_hash'] = $block->previous_block_hash;
        $blockRepresentation['index'] = (int)$block->index;
        $blockRepresentation['mined_by_address'] = $block->mined_by_address;
        $blockRepresentation['difficulty'] = (int)$block->difficulty;
        $blockRepresentation['transactions'] = $block->transactions->map(function($tr){return $tr->hash;});
        return json_encode($blockRepresentation);
    }
    
}