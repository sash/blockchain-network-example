<?php

namespace App\Node;

use App\NodeBlock;

class Difficulty
{
    const CURRENT_MIN_DIFFICULTY = 3;
    
    public function AIsMoreDifficultThenB(NodeBlock $A, NodeBlock $B){
        if ($A->cumulativeDifficulty > $B->cumulativeDifficulty){
            return true;
        } elseif ($A->cumulativeDifficulty == $B->cumulativeDifficulty){
            return $A->block_hash > $B->block_hash;
        } else {
            return false;
        }
    }
    
    public function difficultyOfBlock(NodeBlock $block)
    {
        $difficulty = $this->zeroesInHash($block->block_hash);
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
    
    public function zeroesInHash($block_hash)
    {
        if (preg_match('/^0+/', $block_hash, $matched)){
            return strlen($matched[0]);
        } else {
            return 0;
        }
    }
    
    public function minZeroesInHash(){
        return self::CURRENT_MIN_DIFFICULTY;
    }
}