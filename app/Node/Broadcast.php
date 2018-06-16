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
            echo "Announcing new block $transaction->hash to $knownPeer->host\n";
            $knownPeer->client->broadcastTransaction($transaction->hash);
        });
    }
    
    public function newBlock($block_hash)
    {
        $this->each(function (NodePeer $knownPeer) use ($block_hash) {
            echo "Announcing new block $block_hash to $knownPeer->host\n";
            $knownPeer->client->broadcastBlock($block_hash);
        });
    }
    
    public function newPeer(NodePeer $peer)
    {
        $this->each(function (NodePeer $knownPeer) use ($peer) {
            echo "Announcing $peer->host to $knownPeer->host\n";
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