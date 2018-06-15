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

    /**
     * @param      $address
     * @param null $confirmations
     *
     * @return int
     */
    public function balanceForAddress($address, $confirmations = null){
        $query = NodeTransaction::where('senderAddress', '=', $address)
                ->orWhere('receiverAddress', '=', $address)
                ->selectRaw('CASE WHEN senderAddress = "'.$address.'" THEN -1*(value+fee) ELSE value END as value');


        if ($confirmations !== null){
            $topBlockIndex = $this->blockRepository->getTopBlock()->index;
            $query->withConfirmations($confirmations, $topBlockIndex);
        }

        return intval($query->get()->sum('value'));
    }
}
