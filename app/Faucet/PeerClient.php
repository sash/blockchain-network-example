<?php

namespace App\Faucet;

use App\ApiClient;
use App\Crypto\TransactionSerializer;
use App\NodeTransaction;

class PeerClient extends ApiClient
{
    private $host;
    
    /**
     * PeerClient constructor.
     * @param $host
     */
    function __construct($host)
    {
    
        $this->host = $host;
    }
    
    /**
     * @param NodeTransaction $transaction
     * @throws \App\Exceptions\APIException
     */
    public function postTransaction(NodeTransaction $transaction){
        $ser = (new TransactionSerializer())->serializeTransaction($transaction);
        $ser['hash'] = $transaction->hash;
        $ser['signature'] = $transaction->signature;
        
        return $this->call('/api/transaction', ['transaction' => $ser]);
    }
    
    public function getBalance($address){
        return $this->call('/api/balance/'.$address);
    }
    
    protected function getHost()
    {
        return $this->host;
    }
}