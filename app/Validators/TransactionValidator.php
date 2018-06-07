<?php

namespace App\Validators;

use App\Crypto\PublicKey;
use App\Crypto\TransactionHasher;
use App\Exceptions\InvalidTransaction;
use App\NodeTransaction;

class TransactionValidator
{
    /**
     * @var TransactionHasher
     */
    private $hashTransaction;
    
    /**
     * ValidateTransaction constructor.
     * @param NodeTransaction $transaction
     */
    function __construct(TransactionHasher $hashTransaction)
    {
        $this->hashTransaction = $hashTransaction;
    }
    
    public function isValid(NodeTransaction $transaction){
        $expectedHash = $this->hashTransaction->getHash($transaction);
        if ($expectedHash != $transaction->hash){
            throw new InvalidTransaction('Expected hash: '. $transaction->hash.', got '. $expectedHash);
        }
        $senderPublicKey = PublicKey::fromSignature($transaction);
        if ($senderPublicKey->getAddress() != $transaction->senderAddress){
            throw new InvalidTransaction('Expected sender address: ' . $senderPublicKey->getAddress() . ', got ' . $transaction->senderAddress);
        }
        
    }
}