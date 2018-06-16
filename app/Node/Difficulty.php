<?php

namespace App\Node;

use App\NodeBlock;

class Difficulty
{
    const CURRENT_DIFFICULTY = 4;
    
    public function difficultyOfBlock(NodeBlock $block)
    {
        $difficulty = $this->difficultyOfHash($block->block_hash);
        return pow(16, $difficulty);
    }
    
    /**
     * @param NodeBlock[] $chain
     */
    public function difficultyOfChain($chain)
    {
        return collect($chain)->sum(function($block){
            return $this->difficultyOfBlock($block);
        });
    }
    
    public function difficultyOfHash($block_hash)
    {
        if (preg_match('/^0+/', $block_hash, $matched)){
            return strlen($matched[0]);
        } else {
            return 0;
        }
    }
}