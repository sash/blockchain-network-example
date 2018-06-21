<?php

namespace App\Faucet;

use App\FaucetQueue;

class QueueRepository
{
    /**
     * @return FaucetQueue|null
     */
    public function getTop()
    {
        return FaucetQueue::orderBy('id')->first();
    }
    public function push(FaucetQueue $item)
    {
        $item->save();
    }
    
    public function all()
    {
        return FaucetQueue::orderBy('id')->get();
    }
}