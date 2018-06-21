<?php

namespace App\Node;

use App\NodePeer;
use App\NodeTransaction;
use App\Repository\PeerRepository;

class Broadcast
{
    /**
     * @var PeerRepository
     */
    private $peerRepository;
    
    /**
     * Broadcast constructor.
     * @param PeerRepository $peerRepository
     */
    public function __construct(PeerRepository $peerRepository)
    {
        $this->peerRepository = $peerRepository;
    }
    
    public function newTransaction(NodeTransaction $transaction){
        $this->each(function (NodePeer $knownPeer) use ($transaction) {
    
            error_log("> Announcing new transaction $transaction->hash to $knownPeer->host");
            $knownPeer->client->broadcastTransaction($transaction->hash);
        });
    }
    
    public function newBlock($block_hash)
    {
        $this->each(function (NodePeer $knownPeer) use ($block_hash) {
            error_log("> Announcing new block $block_hash to $knownPeer->host");
            $knownPeer->client->broadcastBlock($block_hash);
        });
    }
    
    public function newPeer(NodePeer $peer)
    {
        $this->each(function (NodePeer $knownPeer) use ($peer) {
            error_log ("> Announcing $peer->host to $knownPeer->host");
            $knownPeer->client->broadcastPeer($peer);
        });
    }
    
    private function each($callback){
        $current = $this->peerRepository->currentPeer();
        
        $this->peerRepository->allPeers()->reject(function($peer) use ($current){
            return $peer->host == $current->host;
        })->each($callback);
    }
}