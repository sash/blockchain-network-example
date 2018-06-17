<?php

namespace App\Http\Controllers;

use App\Http\Resources\NodeBlockResource;
use App\Repository\BlockRepository;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function getLastBlock(BlockRepository $repository)
    {
        $lastBlock = $repository->getTopBlock();
        return new NodeBlockResource($lastBlock);
    }
    
    public function getBlocks(BlockRepository $repository)
    {
        $blocks = $repository->getAllBlocks();
        return NodeBlockResource::collection($blocks);
    }
}
