<?php

namespace App\Http\Controllers;

use App\Http\Resources\NodeBlockResource;
use App\Http\Resources\SimpleNodeBlockResource;
use App\NodeTransaction;
use App\Repository\BlockRepository;
use Illuminate\Http\Request;
use PhpParser\Node;

class BlockController extends Controller
{
    /**
     * @var \App\Repository\BlockRepository
     */
    private $repository;

    /**
     * BlockController constructor.
     *
     * @param \App\Repository\BlockRepository $repository
     */
    public function __construct(BlockRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getLastBlock()
    {
        $lastBlock = $this->repository->getTopBlock();
        return new NodeBlockResource($lastBlock);
    }
    
    public function getBlocks()
    {
        $blocks = $this->repository->getAllBlocks();
        return NodeBlockResource::collection($blocks);
    }

    public function getLastBlocks($limit) {
        $blocks = $this->repository->getLastBlocks($limit);
        return SimpleNodeBlockResource::collection($blocks);
    }

    public function getBlockInfo($blockHash)
    {

        $block = $this->repository->getBlockWithHash($blockHash);

        if(!$block){
            return response('', 404);
        }

        return new NodeBlockResource($block);
    }
}
