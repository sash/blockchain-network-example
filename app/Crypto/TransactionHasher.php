<?php

namespace App\Crypto;

use App\NodeTransaction;

class TransactionHasher
{
    /**
     * @var TransactionSerializer
     */
    private $serializer;
    
    /**
     * HashTransaction constructor.
     * @param NodeTransaction $transaction
     */
    public function __construct(TransactionSerializer $serializer)
    {
        $this->serializer = $serializer;
    }
    
    public function getHash(NodeTransaction $transaction){
        return hash('sha256', json_encode($this->serializer->serializeTransaction($transaction)));
    }
    
}
