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
    //TODO extract it to config
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
    
    /**
     * @param NodeTransaction $transaction
     * @return void
     * @throws InvalidTransaction
     */
    public function assertValid(NodeTransaction $transaction): void{ //, $skipSenderSequence = false
        
        $this->assertHash($transaction);
        
        $this->assertPositive($transaction);
        
        if (!$transaction->isCoinbase) {
    
            $this->assertSignature($transaction);
    
            $this->assertMinimumFee($transaction);
            
//            if (!$skipSenderSequence){
//                $this->assertSenderSequence($transaction);
//            }
    
        }
        
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
     * @throws \Exception
     */
    private function assertSignature(NodeTransaction $transaction): void
    {
        
        $senderPublicKey = PublicKey::fromSignature($transaction);
        if ($senderPublicKey->getAddress() != $transaction->senderAddress) {
            throw new InvalidTransaction('Invalid signature! Expected sender address: ' . $transaction->senderAddress . ', got ' . $senderPublicKey->getAddress().' hash is '.$transaction->hash);
        }
        
    }
    
    /**
     * @param NodeTransaction $transaction
     * @throws InvalidTransaction
     */
    private function assertMinimumFee(NodeTransaction $transaction)
    {
        if ($transaction->fee < self::MINIMUM_FEE) {
            throw new InvalidTransaction('Transaction fee below minimum: ' . self::MINIMUM_FEE);
        }
    }
    
    private function assertPositive(NodeTransaction $transaction)
    {
        if ($transaction->value < 0){
            throw new InvalidTransaction('Transaction value cannot be negative');
        }
    }
//
//    /**
//     * @param NodeTransaction $transaction
//     * @throws InvalidTransaction
//     */
//    private function assertBalance(NodeTransaction $transaction): void
//    {
//        if ($this->transactionRepository->balanceForAddress($transaction->senderAddress, null, $transaction) < $transaction->value + $transaction->fee) {
//            throw new InvalidTransaction('Not enough funds to complete the transaction');
//        }
//    }
    
//    /**
//     * @param NodeTransaction $transaction
//     * @return bool
//     * @throws InvalidTransaction
//     */
//    private function assertSenderSequence(NodeTransaction $transaction)
//    {
//        $existing = $this->transactionRepository->transactionBySenderAndSequence($transaction->senderAddress, $transaction->senderSequence);
//
//        if (!$existing) {
//            return true;
//        }
//
//        if ($existing->hash == $transaction->hash){
//            return true;
//        }
//
//        throw new InvalidTransaction('A transaction with the same sender sequence is already registered');
//
//    }
//
    
}
