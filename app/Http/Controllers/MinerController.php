<?php

namespace App\Http\Controllers;

use App\Http\Resources\NodeBlockResource;
use App\JsonError;
use App\Node\BalanceFactory;
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
//     * @return NodeBlockResource The block to mine
     */
    public function getJob($miner_address, BlockFactory $blockFactory){
//        return [
//            'index' => 0,
//            'difficulty' => 2,
//            'cumulativeDifficulty' => 0,
//            'mined_by_address' => $miner_address,
//            'previous_block_hash' => 'previous-block-hash',
//            'data_hash' => 'data-hash',
//            'block_hash' => 'example-block-hash',
//            'transactions' => [
//                [
//                    'senderAddress' => 'sender-address',
//                    'senderSequence' => 0,
//                    'receiverAddress' => 'receiver-address',
//                    'sequence' => 0,
//                    'value' => 100,
//                    'fee' => 10,
//                    'data' => 'example-data',
//                    'hash' => 'example-hash',
//                    'signature' => 'example-signature',
//                    'timestamp' => 'example-timestamp'
//                ]
//            ],
//            'chain_id' => 'example-chain-id',
//        ];

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
    public function postJob(Request $request, BlockValidator $blockValidator, BlockRepository $blockRepository, BalanceFactory $balanceFactory){
        try{
            $json = $request->json()->all();
            
            $block = NodeBlockResource::fromArray($json['block']);
            $parent = $blockRepository->getTopBlock();
            $blockValidator->assertValidBlock($block, $parent);
            
            $balance = $balanceFactory->forBlock($parent);
            
            $balance->addBlock($block); // assets valid balances in transactions
            
            DB::transaction(function() use($blockRepository, $block, $balance) {
                $block->save();
                $balance->saveForBlock($block);
                $blockRepository->linkTransactions($block);
            });
            return  response('', 201);
        } catch(\Exception $exception){
            return JsonError::fromException($exception)->response(403);
        }
    }
    
    public function getLastBlockHash(BlockRepository $blockRepository){
        try {
            return ['hash' => 'previous-block-hash'];
            return ['hash' => $blockRepository->getTopBlock()->block_hash];
        } catch (\Exception $exception){
            return JsonError::fromException($exception)->response(403);
        }
    }
}
