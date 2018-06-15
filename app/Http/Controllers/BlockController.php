<?php

namespace App\Http\Controllers;

use App\Http\Resources\NodeBlock;
use App\Repository\BlockRepository;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function getLastBlock(BlockRepository $repository){
        
        $lastBlock = $repository->getTopBlock();
        
        return new NodeBlock($lastBlock);
    }
}
