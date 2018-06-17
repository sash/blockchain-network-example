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
        // TODO: Implement
        
    }
    
    public function newBlock($block_hash)
    {
        // TODO: Implement
    }
    
    public function newPeer(NodePeer $peer)
    {
        $this->each(function (NodePeer $knownPeer) use ($peer) {
            echo "Announcing $peer->host to $knownPeer->host\n";
            $knownPeer->client->broadcastPeer($peer);
        });
    }
    
    private function each($callback){
        $this->peerRepository->allPeers()->each($callback);
    }
}