<?php

namespace App\Http\Controllers;

use App\NodeBlock;

class BlockSerializer
{
    
    public function serializeBlock(NodeBlock $block)
    {
        return ['transactions' => []];
    }
}