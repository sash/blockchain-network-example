<?php

namespace App\Repository;

use App\Crypto\BlockHasher;
use App\NodeBlock;
use Illuminate\Database\Eloquent\Collection;

class BlockRepository
{
    
    /**
     * @return NodeBlock
     */
    public function getTopBlock()
    {
        return NodeBlock::orderBy('index', 'desc')->firstOrFail();
    }
    
    /**
     * @return Collection
     */
    public function getAllBlocks()
    {
        return NodeBlock::orderBy('index', 'asc')->get();
    }
    
    public function getGenesisBlock(): NodeBlock{
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
    
    public function getBlockWithHash($block_hash){
        return NodeBlock::where('block_hash', '=', $block_hash)->firstOrFail();
    }
}
