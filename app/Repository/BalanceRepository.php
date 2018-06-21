<?php

namespace App\Repository;

use App\Exceptions\InvalidTransaction;
use App\NodeBalance;
use App\NodeBlock;
use App\NodeTransaction;

class BalanceRepository
{
    /**
     * @param array $balances
     * @param NodeTransaction[] $transactions
     * @param null $outFailedTransactions
     * @return array
     * @deprecated Moved to the Balance class
     */
    public function updateBalances($balances, $transactions, &$outFailedTransactions=null){
        $failedTransactions = [];
        foreach ($transactions as $transaction){
            
            
            if ($balances[$transaction->senderAddress] < $transaction->value + $transaction->fee){
                $failedTransactions[] = $transaction;
                continue;
            }
            
            $balances[$transaction->senderAddress] -= $transaction->value + $transaction->fee;
            $balances[$transaction->receiverAddress] += $transaction->value;
            // Fee is left for the miner's coinbase
        }
        
        $outFailedTransactions = $failedTransactions;
        return $balances;
    }
    
    public function saveBalancesForBlock(NodeBlock $block, $balances){
        
        NodeBalance::where('block_id', '=', $block->id)->delete();
        
        foreach ($balances as $address => $balance){
            $balance = new NodeBalance(['address' => $address, 'balance' => $balance]);
            $balance->block_id = $block->id;
            $balance->save();
        }
    }
    
    public function saveBalancesForPending($balances)
    {
        NodeBalance::whereNull('block_id')->delete();
        foreach ($balances as $address => $balance) {
            $balance = new NodeBalance(['address' => $address, 'balance' => $balance]);
            $balance->block_id = null;
            $balance->save();
        }
    }
    
    public function getBalanceForBlock(NodeBlock $block): array
    {
        return $block->balances->pluck('balance', 'address')->toArray();
    }

    public function getBalanceForAddressOfBlock($address,NodeBlock $block): int
    {
        return $this->getBalanceForBlock($block)[$address];
    }
    
    public function getBalanceForPending(): array
    {
        return NodeBalance::whereNull('block_id')->pluck('balance', 'address');
    }
    
}
