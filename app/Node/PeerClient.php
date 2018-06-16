<?php

namespace App\Node;

use App\Exceptions\APIException;
use App\Http\Resources\NodeBlockResource;
use App\Http\Resources\NodeTransactionResource;
use App\NodeBlock;
use App\NodePeer;
use App\NodeTransaction;

class PeerClient
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
        $this->call('/api/broadcast/block', ['block' => $block_hash, 'peer' => $this->peer->host]);
    }
    
    public function broadcastTransaction($transaction_hash)
    {
        $this->call('/api/broadcast/transaction', ['transaction' => $transaction_hash, 'peer' => $this->peer->host]);
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
    
    
    private function call($endpoint, $data = null)
    {
    
        $url = 'http://' . $this->peer->host . '' . $endpoint;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    
        if ($data != null){
            $data_string = json_encode($data);
            
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Accept: application/json'));
        }
    
        $result = curl_exec($ch);
    
        curl_close($ch);
        if ($result === ''){
            return '';
        }
        $json = json_decode($result, true);
        if ($json === null){
            throw new APIException("Invalid json response - ". $result, 0, $result);
        }
        if (isset($json['success']) && !$json['success']){
            throw new APIException($json['message'].' - '. $json['data'], $json['code'], $json['data']);
        }
        return $json;
    }
    
    
}