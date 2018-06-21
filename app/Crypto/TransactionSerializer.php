<?php

namespace App\Crypto;

use App\NodeTransaction;

class TransactionSerializer
{
    
    public function serializeTransaction(NodeTransaction $transaction)
    {
        $transactionRepresentation = [];
        $transactionRepresentation['from'] = $transaction->senderAddress;
        $transactionRepresentation['from_id'] = $transaction->senderSequence;
        $transactionRepresentation['to'] = $transaction->receiverAddress;
        $transactionRepresentation['value'] = $transaction->value;
        $transactionRepresentation['fee'] = $transaction->fee;
        $transactionRepresentation['data'] = $transaction->data;
        $transactionRepresentation['timestamp'] = $transaction->timestamp;
        return $transactionRepresentation;
    }
}