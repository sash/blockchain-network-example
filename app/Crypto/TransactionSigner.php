<?php

namespace App\Crypto;

use App\NodeTransaction;
use Elliptic\EC;

class TransactionSigner
{
    /**
     * @var TransactionHasher
     */
    private $transactionHasher;
    
    /**
     * TransactionSigner constructor.
     * @param TransactionHasher $transactionHasher
     */
    public function __construct(TransactionHasher $transactionHasher)
    {
        $this->transactionHasher = $transactionHasher;
    }
    
    public function sign($privateKeyAsHex, NodeTransaction $transaction){
        $hash = $transaction->hash ?: $this->transactionHasher->getHash($transaction);
        
        $keyPair = PublicPrivateKeyPair::fromPrivateKey($privateKeyAsHex);
        return $keyPair->sign($hash);
    }
}