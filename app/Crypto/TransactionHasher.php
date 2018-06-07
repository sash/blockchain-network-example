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
    
    private function serializedTransaction(NodeTransaction $transaction)
    {
        $transactionRepresentation = [];
        $transactionRepresentation['from'] = $transaction->senderAddress;
        $transactionRepresentation['to'] = $transaction->receiverAddress;
        $transactionRepresentation['value'] = $transaction->value;
        $transactionRepresentation['fee'] = $transaction->fee;
        $transactionRepresentation['datetime'] = $transaction->created_at->getTimestamp();
        $transactionRepresentation['public_key'] = $transaction->senderPublicKey;
        return json_encode($transactionRepresentation);
    }
    
}