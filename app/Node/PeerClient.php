<?php

namespace App\Node;

use App\ApiClient;
use App\Exceptions\APIException;
use App\Http\Resources\NodeBlockResource;
use App\Http\Resources\NodeTransactionResource;
use App\NodeBlock;
use App\NodePeer;
use App\NodeTransaction;
use App\Repository\PeerRepository;

class PeerClient extends ApiClient
{
    /**
     * @var NodePeer
     */
    private $peer;
    
    /**
     * PeerClient constructor.
     * @param NodePeer $peer
     */
    public function __construct(NodePeer $peer)
    {
        $this->peer = $peer;
    }
    
    
    /**
     * @return NodeBlock[]
     * @throws \Exception
     */
    function getBlocks()
    {
        $json = $this->call('/api/blocks');
        if (!is_array($json)) {
            throw new \Exception('Invalid response from peer');
        }
        
        $this->peer->wasActive();
        
        return collect($json)->map(function ($block) {
            return NodeBlockResource::fromArray($block);
        })->toArray();
    }
//
//    /**
//     * @param $transactionHash
//     * @return NodeTransaction
//     * @throws \Exception
//     * @deprecated No Usages! Delete!
//     */
//    public function getTransaction($transactionHash)
//    {
//        $transactionJson = $this->call('/api/transactions/'.$transactionHash);
//        $transaction = NodeTransactionResource::fromArray($transactionJson);
//
//        if ($transaction->hash != $transactionHash) {
//            throw new \Exception('The hash of the transaction fetched from the node does not match the requested hash!');
//        }
//
//        return $transaction;
//    }
    
    public function broadcastPeer(NodePeer $peer)
    {
        $res = $this->call('/api/broadcast/peer', ['peer' => $peer->host]);
        
    }
    
    public function broadcastBlock($block_hash)
    {
        $this->call('/api/broadcast/block', ['block' => $block_hash, 'peer' => (new PeerRepository())->currentPeer()->host]);
    }
    
    public function broadcastTransaction($transaction_hash)
    {
        $this->call('/api/broadcast/transaction', ['transaction' => $transaction_hash, 'peer' => (new PeerRepository())->currentPeer()->host]);
    }
    
    /**
     * @return NodePeer[]
     */
    public function getPeers()
    {
        $hosts = $this->call('/api/broadcast/peers');
        
        
        $this->peer->wasActive();
        
        return array_map(function ($host) {
            return new NodePeer(['host' => $host]);
        }, $hosts);
    }
    
    public function getLastBlock(): NodeBlock
    {
        $blockJson = $this->call('/api/blocks/last');
    
        $this->peer->wasActive();
        
        return NodeBlockResource::fromArray($blockJson);
    }
    
    /**
     * @param $transaction_hash
     * @return NodeTransaction
     * @throws APIException
     * @throws \Exception
     */
    public function getTransaction($transaction_hash): NodeTransaction
    {
        $transaction = $this->call('/api/transactions/'.$transaction_hash);
        $this->peer->wasActive();
        $transactionObject = NodeTransactionResource::fromArray($transaction);
        if ($transactionObject->hash != $transaction_hash){
            throw new \Exception('Invalid transaction returned from peer');
        }
        
        return $transactionObject;
        
    }
    
    
    protected function getHost()
    {
        return $this->peer->host;
    }
}