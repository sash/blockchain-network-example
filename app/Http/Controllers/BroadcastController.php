<?php

namespace App\Http\Controllers;

use App\Http\Resources\NodeBlockResource;
use App\Http\Resources\NodeTransactionResource;
use App\Jobs\SyncChain;
use App\JsonError;
use App\Node\BalanceFactory;
use App\Node\Broadcast;
use App\Node\Difficulty;
use App\NodeBlock;
use App\NodePeer;
use App\Repository\BlockRepository;
use App\Repository\PeerRepository;
use App\Repository\TransactionRepository;
use App\Validators\BlockValidator;
use App\Validators\TransactionValidator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            Broadcast $rebroadcast
    ) {
        try{
            // TODO: The post block only contains the hash! Request the full block from the peer if the block is new!
            $this->blockRepository = $blockRepository;
            $this->transactionRepository = $transactionRepository;
            $this->transactionValidator = $transactionValidator;
            $bodyArray = $request->json();
            if (!isset($bodyArray['block'])){
                return (new JsonError('Missing block'))->response(401);
            }
            
            $blockHash = $bodyArray['block'];
            
            if (!isset($bodyArray['peer'])){
                return (new JsonError('Missing peer'))->response(401);
            }
            $peer = $peerRepository->getPeer($bodyArray['peer']);
            
            
            
//            $block = NodeBlockResource::fromArray($blockArray);
            
            if ($blockRepository->getBlockWithHash($blockHash)){
                return response('', 201);
            }
            
            $sync = new SyncChain($peer, $validator, $blockRepository, $this->difficulty, $transactionValidator, $transactionRepository);
            dispatch_now($sync);
    
            $rebroadcast->newBlock($blockHash);
            return response();
        } catch (\Exception $exception){
            return JsonError::fromException($exception)->response(401);
        }
    }
    
    public function postTransaction(
            Request $request,
            TransactionValidator $transactionValidator,
            Broadcast $rebroadcast,
            PeerRepository $peerRepository,
            BalanceFactory $balanceFactory
    ) {
        // TODO: The post transaction only contains the hash! Request the full transaction from the peer if the transaction is new!
        try{
            $bodyArray = $request->json();
            if (!isset($bodyArray['transaction'])) {
                return (new JsonError('Missing transaction'))->response(401);
            }
        
            if (!isset($bodyArray['peer'])) {
                return (new JsonError('Missing peer'))->response(401);
            }
            
            $existingTransaction = NodeTransactionResource::where('hash', '=', $bodyArray['transaction']['hash'])->first();
            if ($existingTransaction){
                return response('',201);
            }
            
            $peerRepository->getPeer($bodyArray['peer'])->wasActive();
            
            $transaction = NodeTransactionResource::fromArray($bodyArray['transaction']);
            $transactionValidator->assertValid($transaction);
            
            $balance = $balanceFactory->forCurrentPending();
            
            $balance->addTransaction($transaction);
            
            
            $transaction->save();
            $balance->savePending();
            
            $rebroadcast->newTransaction($transaction);
            return response();
        } catch (\Exception $exception) {
            return JsonError::fromException($exception)->response(401);
        }
    }
    
    public function getPeers(PeerRepository $peerRepository){
        return array_map( function($peer){return $peer->host;}, $peerRepository->allPeers());
    }
    
    public function postPeer(Request $request, PeerRepository $peerRepository, Broadcast $broadcast){
        $json = $request->json();
        
        $peer = $peerRepository->getPeer($json['peer']);
        
        if ($peer->host == $peerRepository->currentPeer()->host){
            return response('', 201);
        }
        
        if ($peer->is_new){
            $broadcast->newPeer($peer);
        }
        
        $peer->wasActive();
        
        return response();
    }
    
    
}
