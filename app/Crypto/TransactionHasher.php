<?php

namespace App\Crypto;

use App\NodeTransaction;

class TransactionHasher
{
    
    /**
     * HashTransaction constructor.
     * @param NodeTransaction $transaction
     */
    public function __construct()
    {
    }
    
    public function getHash(NodeTransaction $transaction){
        return hash('sha256', $this->serializedTransaction($transaction));
    }
    
    public function serializedTransaction(NodeTransaction $transaction)
    {
        $transactionRepresentation = [];
        $transactionRepresentation['from'] = $transaction->senderAddress;
        $transactionRepresentation['from_id'] = $transaction->senderSequence;
        $transactionRepresentation['to'] = $transaction->receiverAddress;
        $transactionRepresentation['value'] = $transaction->value;
        $transactionRepresentation['fee'] = $transaction->fee;
        $transactionRepresentation['data'] = $transaction->data;
        $transactionRepresentation['timestamp'] = $transaction->timestamp;
        return json_encode($transactionRepresentation);
    }
    
}
