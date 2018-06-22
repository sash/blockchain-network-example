<?php

namespace App\Node;

use App\Crypto\PublicPrivateKeyPair;
use App\Crypto\TransactionHasher;
use App\NodeBlock;
use App\NodeTransaction;
use App\Repository\TransactionRepository;
use App\Validators\BlockValidator;
use Carbon\Carbon;

class TransactionFactory
{
    /**
     * @var TransactionHasher
     */
    private $hasher;
    
    /**
     * TransactionFactory constructor.
     * @param TransactionRepository $transactionRepository
     * @param TransactionHasher $hasher
     */
    function __construct(TransactionHasher $hasher)
    {
        $this->hasher = $hasher;
    }
    
    function buildCoinbaseForBlock(NodeBlock $block): NodeTransaction
    {
        $due = BlockValidator::COINBASE_MINING_FEE;
        $alreadyPaidOut = 0;
        
        foreach ($block->transactions as $transaction){
            if ($transaction->isCoinbase){
                $alreadyPaidOut += $transaction->value;
            } else {
                $due += $transaction->fee;
            }
        }
        
        if ($due <= $alreadyPaidOut) {
            throw new \InvalidArgumentException('The block has paid out all the coinbase it offers!');
        }
            // Build the coninbase transaction
        $res = new NodeTransaction([
            'senderAddress' => NodeTransaction::COINBASE_ADDRESS,
            'receiverAddress' => $block->mined_by_address,
            'value' => $due - $alreadyPaidOut,
            'fee' => 0,
            'data' => 'Fee for mining the block',
            'timestamp' => Carbon::now()->timestamp,
            'senderSequence' => 0, // Not applicable
            'block_id' => $block->id,
        ]);
        $res->hash = $this->hasher->getHash($res);
        $res->signature = str_repeat('0', 130);
        return $res;
    }
    
    function buildSpendTransaction(PublicPrivateKeyPair $key, $seq, $value, $fee, $toAddress, $data){
        $fromAddress = $key->getAddress();
        
        $res = new NodeTransaction([
                'senderAddress'   => $fromAddress,
                'senderSequence'   => $seq,
                'receiverAddress' => $toAddress,
                'value'           => $value,
                'fee'             => $fee,
                'data'            => $data,
                'timestamp'       => Carbon::now()->timestamp,
        ]);
        $res->hash = $this->hasher->getHash($res);
        $res->signature = $key->sign($res->hash);
        return $res;
    }
}