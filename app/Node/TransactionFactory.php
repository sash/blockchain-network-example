<?php

namespace App\Node;

use App\NodeBlock;
use App\NodeTransaction;
use App\Validators\BlockValidator;
use Carbon\Carbon;

class TransactionFactory
{
 
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
        return new NodeTransaction([
            'senderAddress' => NodeTransaction::COINBASE_ADDRESS,
            'receiverAddress' => $block->mined_by_address,
            'value' => $due - $alreadyPaidOut,
            'fee' => 0,
            'data' => 'Fee for mining the block',
            'timestamp' => Carbon::now()->timestamp,
            'senderSequence' => 0, // Not applicable
            'block_id' => $block->id,
        ]);
            
    }
}