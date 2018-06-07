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
        $crypto = new EC('secp256k1');
        $key = $crypto->keyFromPrivate($privateKeyAsHex, 'hex');
        
        $signature = $key->sign($hash, 'hex', ['canonical' => true]);
    
        return $signature->r->toString('hex'). $signature->s->toString('hex'). str_pad(dechex($signature->recoveryParam),2,'0', STR_PAD_LEFT);
    }
}