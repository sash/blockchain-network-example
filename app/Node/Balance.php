<?php

namespace App\Node;

use App\NodeBlock;
use App\NodeTransaction;
use App\Repository\BlockRepository;

class Balance
{
    /**
     * @var BlockRepository
     */
    private $blockRepository;
    
    /**
     * Balance constructor.
     * @param BlockRepository $blockRepository
     */
    public function __construct(BlockRepository $blockRepository)
    {
        $this->blockRepository = $blockRepository;
    }
    
    public function loadAddress($address){
        $this->transactions = NodeTransaction::where('senderAddress', '=', $address)->orWhere('receiverAddress', '=', $address);
        // Fetch all transactions that are from or to this address in order of the mined blocks
        return $this;
    }
    
    /**
     * @param int|null $confirmations 0 = no confirmations (last balance), null = inclusing pending, 1, 2, 3 = that many confirmations top of the block
     *
     */
    public function getBalance($confirmations = 0)
    {
        if ($confirmations !== null){
            $topBlockIndex = $this->getTopBlockIndex();
            $this->transactions->where('minedInBlockIndex', '<=',
                    $topBlockIndex - $confirmations)->whereNotNull('minedInBlockIndex');
        }
        
        // null will mean all transactions known to the node (including the ones that are in fact in the pending pool)
    }
    
    private function getTopBlockIndex()
    {
        $this->blockRepository->getTopBlock()->index;
    }
}