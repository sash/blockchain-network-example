<?php

namespace App\Node;

use App\Http\Resources\NodeBlockResource;
use App\NodeBlock;
use App\NodePeer;
use App\NodeTransaction;
use App\Repository\BlockRepository;
use App\Repository\PeerRepository;

trait PeerCommunicationTrait
{
    
    /**
     * @return NodeBlock[]
     * @throws \Exception
     */
    function getBlocks()
    {
        $json = $this->call('/api/blocks');
        if (!is_array($json)){
            throw new \Exception('Invalid response from peer');
        }
        
        $this->wasActive();
        
        return collect($json)->map(function($block){return NodeBlockResource::fromArray($block);})->toArray();
    }
    
    /**
     * @param $transactionHash
     * @return NodeTransaction
     * @throws \Exception
     */
    public function getTransaction($transactionHash)
    {
        // TODO: Implement
        $transaction = new NodeTransaction();
        
        if ($transaction->hash != $transactionHash) {
            throw new \Exception('The hash of the transaction fetched from the node does not match the requested hash!');
        }
        
        return $transaction;
    }
    
    public function broadcastPeer(NodePeer $peer){
        $this->call('/api/bloadcast/peer', ['peer' => $peer->host]);
    }
    
    /**
     * @return NodePeer[]
     */
    public function getPeers(){
        $hosts = $this->call('/api/broadcast/peers');
        return array_map(function($host){return new NodePeer(['host' => $host]);}, $hosts);
    }
    
    public function getLastBlockHash()
    {
        // TODO: Implement
        return '';
    }
    
    
    private function call($endpoint, $data = null)
    {
        if ($data != null){
            // POST
        } else {
            // GET
        }
        // TODO: Implement
        return [];
    }
}