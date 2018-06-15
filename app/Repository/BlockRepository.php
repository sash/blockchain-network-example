<?php

namespace App\Repository;

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
}
