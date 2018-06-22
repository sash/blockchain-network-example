<?php

namespace App\Jobs;

use App\Node\BalanceFactory;
use App\NodeBlock;
use App\Repository\BalanceRepository;
use App\Repository\BlockRepository;
use App\Repository\TransactionRepository;

class UpdateBlockBalance
{
    /**
     * @var BalanceFactory
     */
    private $balanceFactory;
    /**
     * @var BlockRepository
     */
    private $blockRepository;
    
    /**
     * UpdatePendingBalance constructor.
     * @param BalanceFactory $balanceFactory
     * @param BalanceRepository $balanceRepository
     * @param TransactionRepository $transactionRepositoryß
     */
    function __construct(
            BalanceFactory $balanceFactory,
            BlockRepository $blockRepository
    ) {
        $this->balanceFactory = $balanceFactory;
        $this->blockRepository = $blockRepository;
    }
    
    public function update(NodeBlock $block){
        $parent = $this->blockRepository->getBlockWithHash($block->previous_block_hash);
        
        $balance = $this->balanceFactory->forBlock($parent);
        $balance->addBlock($block); // assets valid balances in transactions
        $balance->saveForBlock($block);
    }
}