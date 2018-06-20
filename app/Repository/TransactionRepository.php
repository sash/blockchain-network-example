<?php

namespace App\Repository;

use App\NodeBalance;
use App\NodeTransaction;
use Illuminate\Database\Eloquent\Collection;
use PhpParser\Node;

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
     * @deprecated Alternative implementation is in place - see the NodeBalance object and the BalanceRepository
     */
    public function balanceForAddress($address, $confirmations = null, NodeTransaction $beforeTransaction=null){
        $query = NodeTransaction::where(function($query) use ($address){
            $query
                    ->where('senderAddress', '=', $address)
                    ->orWhere('receiverAddress', '=', $address);
        })
        ->selectRaw('CASE WHEN senderAddress = "'.$address.'" THEN -1*(value+fee) ELSE value END as value');

        if ($beforeTransaction && $beforeTransaction->id){
            $query->where('id', '<', $beforeTransaction->id);
        }

        if ($confirmations !== null){
            $topBlockIndex = $this->blockRepository->getTopBlock()->index;
            $query->withConfirmations($confirmations, $topBlockIndex);
        }

        return intval($query->get()->sum('value'));
    }
    
    /**
     * @return NodeTransaction[]|Collection
     */
    public function pendingTransactions()
    {
        return NodeTransaction::whereNull('block_id');
    }
    
    /**
     * @param $senderAddress
     * @param $senderSequence
     * @return NodeTransaction|null
     */
    public function transactionBySenderAndSequence($senderAddress, $senderSequence)
    {
        return NodeTransaction::where('senderAddress','=',$senderAddress)->where('senderSequence', '=',
                $senderSequence)->first();
    }
    
    public function transactionsBySender($senderAddress){
        return  NodeTransaction::where('senderAddress', '=', $senderAddress);
    }
}
