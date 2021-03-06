<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitMinedBlock;
use App\Http\Resources\NodeBlockResource;
use App\Jobs\UpdatePendingBalance;
use App\JsonError;
use App\Node\BalanceFactory;
use App\Node\BlockFactory;
use App\Node\Broadcast;
use App\Node\Difficulty;
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
    public function postJob(SubmitMinedBlock $request, BlockValidator $blockValidator, BlockRepository $blockRepository, BalanceFactory $balanceFactory, Broadcast $broadcast, Difficulty $difficulty, UpdatePendingBalance $updatePendingBalance){
        try{
            //dd($request->json());
            $block = NodeBlockResource::fromArray($request->get('block'));
            $parent = $blockRepository->getTopBlock();
            $blockValidator->assertValidBlock($block, $parent);

            $balance = $balanceFactory->forBlock($parent);
            $balance->addBlock($block); // assets valid balances in transactions
            
            $block->cumulativeDifficulty = $parent->cumulativeDifficulty + $difficulty->difficultyOfBlock($block);

            DB::transaction(function() use($blockRepository, $block, $balance) {
                $block->save();
                $balance->saveForBlock($block);
                $blockRepository->linkTransactions($block);
            });
            $updatePendingBalance->update();
            $broadcast->newBlock($block->block_hash);
            return  response('', 201);
        } catch(\Exception $exception){
            error_log("Error: ". $exception->getMessage());
            return JsonError::fromException($exception)->response(403);
        }
    }
    
    public function getLastBlockHash(BlockRepository $blockRepository){
        try {
            return ['hash' => $blockRepository->getTopBlock()->block_hash];
        } catch (\Exception $exception){
            return JsonError::fromException($exception)->response(403);
        }
    }
}
