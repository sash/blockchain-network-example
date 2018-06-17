<?php

namespace App\Http\Controllers;

use App\Http\Resources\NodeBlockResource;
use App\JsonError;
use App\Node\BlockFactory;
use App\Repository\BlockRepository;
use App\Validators\BlockValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MinerController extends Controller
{
    /**
     * Construct a candidate block and pass it off to the miner
     * @param string $miner_address
     * @return NodeBlockResource The block to mine
     */
    public function getJob($miner_address, BlockFactory $blockFactory){
        $block = $blockFactory->buildMostProfitableFromPending($miner_address);
        return new NodeBlockResource($block);
    }
    
    
    /**
     * Post the block candidate to the node
     * @param Request $request
     * @param BlockValidator $blockValidator
     * @param BlockRepository $blockRepository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postJob(Request $request, BlockValidator $blockValidator, BlockRepository $blockRepository){
        try{
            $json = $request->json()->all();
            
            $block = NodeBlockResource::fromArray($json['block']);
            $blockValidator->assertValidBlock($block, $blockRepository->getTopBlock());
        
            DB::transaction(function() use($blockRepository, $block) {
                $block->save();
                $blockRepository->linkTransactions($block);
            });
            return  response('', 201);
        } catch(\Exception $exception){
            return JsonError::fromException($exception)->response(403);
        }
    }
    
    public function getLastBlockHash(BlockRepository $blockRepository){
        return ['hash' => $blockRepository->getTopBlock()->block_hash];
    }
}
