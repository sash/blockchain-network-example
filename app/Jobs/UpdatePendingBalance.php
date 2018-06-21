<?php

namespace App\Jobs;

use App\Exceptions\InvalidTransaction;
use App\Node\BalanceFactory;
use App\Repository\BalanceRepository;
use App\Repository\BlockRepository;
use App\Repository\TransactionRepository;

class UpdatePendingBalance
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
     * @var TransactionRepository
     */
    private $transactionRepository;
    
    /**
     * UpdatePendingBalance constructor.
     * @param BalanceFactory $balanceFactory
     * @param BalanceRepository $balanceRepository
     * @param TransactionRepository $transactionRepositoryß
     */
    function __construct(BalanceFactory $balanceFactory, BlockRepository $blockRepository, TransactionRepository $transactionRepository)
    {
        $this->balanceFactory = $balanceFactory;
        $this->blockRepository = $blockRepository;
        $this->transactionRepository = $transactionRepository;
    }
    
    public function update()
    {
        $balance = $this->balanceFactory->forBlock($this->blockRepository->getTopBlock());
        $pendingTransactions = $this->transactionRepository->pendingTransactions()->orderBy('fee', 'desc')->get();
        foreach ($pendingTransactions as $transaction) {
            try {
                $balance->addTransaction($transaction);
            } catch (InvalidTransaction $exception) {
            
            }
        }
        $balance->savePending();
    }
}