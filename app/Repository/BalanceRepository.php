<?php

namespace App\Repository;

use App\Exceptions\InvalidTransaction;
use App\NodeBalance;
use App\NodeBlock;
use App\NodeTransaction;

class BalanceRepository
{
    
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
    
    public function getBalanceForPending(): array
    {
        return NodeBalance::whereNull('block_id')->pluck('balance', 'address')->toArray();
    }
    
    
}