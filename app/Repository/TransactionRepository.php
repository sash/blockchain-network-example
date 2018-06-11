<?php

namespace App\Repository;

use App\NodeTransaction;

class TransactionRepository
{
    /**
     * @var BlockRepository
     */
    private $blockRepository;
    
    /**
     * TransactionRepository constructor.
     * @param BlockRepository $blockRepository
     */
    public function __construct(BlockRepository $blockRepository)
    {
        $this->blockRepository = $blockRepository;
    }
    
    public function balanceForAddress($address, $confirmations){
        $query = NodeTransaction::valid()
                ->where('senderAddress', '=', $address)
                ->orWhere('receiverAddress', '=', $address)
                ->selectRaw('IF(senderAddress == "'.$address.'", -1*(value+fee), value) as balance');
        
        if ($confirmations !== null){
            $topBlockIndex = $this->blockRepository->getTopBlock()->index;
            $query->withConfirmations($confirmations, $topBlockIndex);
        }
        return $query->sum('balance');
    }
}