<?php

namespace App\Validators;

use App\Crypto\PublicKey;
use App\Crypto\TransactionHasher;
use App\Exceptions\InvalidTransaction;
use App\Node\Balance;
use App\NodeTransaction;
use App\Repository\TransactionRepository;

class TransactionValidator
{
    const MINIMUM_FEE = 10;
    /**
     * @var TransactionHasher
     */
    private $hashTransaction;
    
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;
    
    /**
     * ValidateTransaction constructor.
     * @param TransactionHasher $hashTransaction
     * @param TransactionRepository $transactionRepository
     */
    function __construct(TransactionHasher $hashTransaction, TransactionRepository $transactionRepository)
    {
        $this->hashTransaction = $hashTransaction;
        $this->transactionRepository = $transactionRepository;
    }
    
    public function isValid(NodeTransaction $transaction){
        $this->assertHash($transaction);
        
        $this->assertSignature($transaction);
    
        $this->assertMinimumFee($transaction);
        
        $this->assertBalance($transaction);
    
        return true;
    }
    
    /**
     * @param NodeTransaction $transaction
     * @throws InvalidTransaction
     */
    private function assertHash(NodeTransaction $transaction): void
    {
        
        $expectedHash = $this->hashTransaction->getHash($transaction);
        if (!$transaction->hash){
            $transaction->hash = $expectedHash;
        }
        if ($expectedHash != $transaction->hash) {
            throw new InvalidTransaction('Expected hash: ' . $transaction->hash . ', got ' . $expectedHash);
        }
    }
    
    /**
     * @param NodeTransaction $transaction
     * @throws InvalidTransaction
     */
    private function assertSignature(NodeTransaction $transaction): void
    {
        $senderPublicKey = PublicKey::fromSignature($transaction);
        if ($senderPublicKey->getAddress() != $transaction->senderAddress) {
            throw new InvalidTransaction('Expected sender address: ' . $transaction->senderAddress . ', got ' . $senderPublicKey->getAddress());
        }
    }
    
    private function assertMinimumFee(NodeTransaction $transaction)
    {
        if ($transaction->fee < self::MINIMUM_FEE) {
            throw new InvalidTransaction('Transaction fee below minimum: ' . self::MINIMUM_FEE);
        }
    }
    
    /**
     * @param NodeTransaction $transaction
     * @throws InvalidTransaction
     * @deprecated The balance check needs to be refactored to support transaction validation for transactions already in a block
     */
    private function assertBalance(NodeTransaction $transaction): void
    {
        if ($this->transactionRepository->balanceForAddress($transaction->senderAddress, null) < $transaction->value + $transaction->fee) {
            throw new InvalidTransaction('Not enough funds to complete the transaction');
        }
    }
    
}