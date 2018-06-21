<?php

namespace App\Repository;

use App\Exceptions\InvalidTransaction;
use App\Node\BalanceFactory;
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
     * @param BalanceFactory $balanceFactory
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
        if ($confirmations === null){
            return $this->unconfirmedBalanceForAddress($address);
        }
        else {
            return $this->confirmedBalanceForAddress($address, $confirmations);
        }
//
//        $query = NodeTransaction::where(function($query) use ($address){
//            $query
//                    ->where('senderAddress', '=', $address)
//                    ->orWhere('receiverAddress', '=', $address);
//        })
//        ->selectRaw('CASE WHEN senderAddress = "'.$address.'" THEN -1*(value+fee) ELSE value END as value');
//
//        if ($beforeTransaction && $beforeTransaction->id){
//            $query->where('id', '<', $beforeTransaction->id);
//        }
//
//        if ($confirmations !== null){
//            $topBlockIndex = $this->blockRepository->getTopBlock()->index;
//            $query->withConfirmations($confirmations, $topBlockIndex);
//        }
//
//        return intval($query->get()->sum('value'));
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
    
    private function confirmedBalanceForAddress($address, $confirmations)
    {
        $topBlockIndex = $this->blockRepository->getTopBlock()->index;
    
        
        $targetBlock = $this->blockRepository->getBlockWithIndex($topBlockIndex - $confirmations + 1);
        if (!$targetBlock){
            throw new \InvalidArgumentException('The chain is less then '.$confirmations.' long');
        }
    
    
        
        $balance = NodeBalance::where('address', '=', $address)->where('block_id', '=', $targetBlock->id)->first();
        if (!$balance){
            return 0;
        }
        return $balance->balance;
    }
    
    private function unconfirmedBalanceForAddress($address)
    {
        $balance = NodeBalance::where('address', '=', $address)->whereNull('block_id')->first();
        if (!$balance) {
            return 0;
        }
        return $balance->balance;
    }
    
    public function transactionsByHash($hash)
    {
        return NodeTransaction::where('hash', '=', $hash)->first();
    }
}
