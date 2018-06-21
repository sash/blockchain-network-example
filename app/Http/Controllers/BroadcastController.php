<?php

namespace App\Http\Controllers;

use App\Http\Resources\NodeBlockResource;
use App\Http\Resources\NodeTransactionResource;
use App\Jobs\SyncChain;
use App\Jobs\UpdatePendingBalance;
use App\JsonError;
use App\Node\BalanceFactory;
use App\Node\Broadcast;
use App\Node\Difficulty;
use App\NodeBlock;
use App\NodePeer;
use App\NodeTransaction;
use App\Repository\BalanceRepository;
use App\Repository\BlockRepository;
use App\Repository\PeerRepository;
use App\Repository\TransactionRepository;
use App\Validators\BlockValidator;
use App\Validators\TransactionValidator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psy\Util\Json;

class BroadcastController extends Controller
{
    /**
     * @var TransactionValidator
     */
    private $transactionValidator;
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;
    /**
     * @var Difficulty
     */
    private $difficulty;
    /**
     * @var BlockRepository
     */
    private $blockRepository;
    
    /**
     * BroadcastController constructor.
     * @param Difficulty $difficulty
     * @param BlockRepository $blockRepository
     */
    public function __construct(Difficulty $difficulty)
    {
        $this->difficulty = $difficulty;
    }
    
    /**
     * Post a new block to the chain. The block is found by a miner of another peer
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postBlock(
            Request $request,
            BlockValidator $validator,
            TransactionValidator $transactionValidator,
            PeerRepository $peerRepository,
            BlockRepository $blockRepository,
            TransactionRepository $transactionRepository,
            Broadcast $rebroadcast,
            UpdatePendingBalance $updatePendingBalance
    ) {
        try{
            $this->blockRepository = $blockRepository;
            $this->transactionRepository = $transactionRepository;
            $this->transactionValidator = $transactionValidator;
            $bodyArray = $request->json()->all();
            if (!isset($bodyArray['block'])){
                return (new JsonError('Missing block'))->response(403);
            }
            
            $blockHash = $bodyArray['block'];
            
            if (!isset($bodyArray['peer'])){
                return (new JsonError('Missing peer'))->response(403);
            }
            $peer = $peerRepository->getPeer($bodyArray['peer']);
            
            
            
//            $block = NodeBlockResource::fromArray($blockArray);
            
            if ($blockRepository->getBlockWithHash($blockHash)){
                return response('', 201);
            }
            
            $sync = new SyncChain($peer, $validator, $blockRepository, $this->difficulty, $transactionValidator, $transactionRepository,
                    $updatePendingBalance);
            dispatch_now($sync);
            
            error_log("< New block - ".$blockHash);
    
            $rebroadcast->newBlock($blockHash);
            return response();
        } catch (\Exception $exception){
            return JsonError::fromException($exception)->response(403);
        }
    }
    
    public function postTransaction(
            Request $request,
            TransactionValidator $transactionValidator,
            Broadcast $rebroadcast,
            PeerRepository $peerRepository,
            BalanceFactory $balanceFactory
    ) {
        try{
            $bodyArray = $request->json()->all();
            if (!isset($bodyArray['transaction'])) {
    
                return (new JsonError('Missing transaction'))->response(403);
            }
        
            if (!isset($bodyArray['peer'])) {
                return (new JsonError('Missing peer'))->response(403);
            }
            $existingTransaction = NodeTransaction::where('hash', '=', $bodyArray['transaction'])->first();
            if ($existingTransaction){
    
                return response('',201);
            }
    
            $peer = $peerRepository->getPeer($bodyArray['peer'])->wasActive();
    
            $transaction = $peer->client->getTransaction($bodyArray['transaction']);
            
            $transactionValidator->assertValid($transaction);
            
            $balance = $balanceFactory->forCurrentPending();
    
            $balance->addTransaction($transaction);
    
            $transaction->save();
            $balance->savePending();
    
            error_log("< New Transaction - " . $transaction->hash);
            
            $rebroadcast->newTransaction($transaction);
            return response();
        } catch (\Exception $exception) {
            return JsonError::fromException($exception)->response(403);
        }
    }
    
    public function getPeers(PeerRepository $peerRepository){
        try{
            $peers = $peerRepository->allPeers()->all();
            return array_map( function($peer){
                return $peer->host;
                }, $peers);
        }catch (\Throwable $e){
            return JsonError::fromException($e)->response(500);
        }
    }
    
    public function postPeer(Request $request, PeerRepository $peerRepository, Broadcast $broadcast){
        try {
            $json = $request->json()->all();
    
            $peer = $peerRepository->getPeer($json['peer']);
    
            if ($peer->host == $peerRepository->currentPeer()->host) {
                return response('', 201);
            }
    
            if ($peer->is_new) {
                $broadcast->newPeer($peer);
            }
    
            $peer->wasActive();
            error_log("< New peer accepted -".$peer->host);
    
            return response();
        } catch (\Throwable $e) {
            return JsonError::fromException($e)->response(500);
        }
    }
    
    
}
