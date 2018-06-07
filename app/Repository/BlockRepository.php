<?php

namespace App\Repository;

use App\NodeBlock;

class BlockRepository
{
    
    public function getTopBlock()
    {
        return NodeBlock::all()->sortBy('index')->whereNotNull('hash')
    }
}