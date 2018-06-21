<?php

namespace App\Node;

use App\Crypto\BlockHasher;
use App\NodeBlock;
use App\NodeTransaction;
use App\Repository\BlockRepository;
use App\Repository\TransactionRepository;
use App\Validators\BlockValidator;

class BlockFactory
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;
    /**
     * @var BlockRepository
     */
    private $blockRepository;
    /**
     * @var BlockHasher
     */
    private $blockHasher;
    /**
     * @var BalanceFactory
     */
    private $balanceFactory;
    /**
     * @var TransactionFactory
     */
    private $transactionFactory;
    /**
     * @var \App\Validators\BlockValidator
     */
    private $blockValidator;

    /**
     * BlockFactory constructor.
     *
     * @param TransactionRepository          $transactionRepository
     * @param BlockRepository                $blockRepository
     * @param BlockHasher                    $blockHasher
     * @param BalanceFactory                 $balanceFactory
     * @param TransactionFactory             $transactionFactory
     * @param \App\Validators\BlockValidator $blockValidator
     */
    function __construct(TransactionRepository $transactionRepository, BlockRepository $blockRepository, BlockHasher $blockHasher, BalanceFactory $balanceFactory, TransactionFactory $transactionFactory, BlockValidator $blockValidator)
    {
        $this->transactionRepository = $transactionRepository;
        $this->blockRepository = $blockRepository;
        $this->blockHasher = $blockHasher;
        $this->balanceFactory = $balanceFactory;
        $this->transactionFactory = $transactionFactory;
        $this->blockValidator = $blockValidator;
    }
    
    function buildMostProfitableFromPending($miner_address): NodeBlock
    {
        $parent = $this->blockRepository->getTopBlock();

        $res = new NodeBlock();
        $res->difficulty = (new Difficulty)->minZeroesInHash();
        $res->previous_block_hash = $parent->block_hash;
        $res->index = $parent->index + 1;
        $res->mined_by_address = $miner_address;
        
        $balance = $this->balanceFactory->forBlock($parent);

        $pendingTransactions = $this->transactionRepository->pendingTransactions()->orderBy('fee', 'desc')->get();
        foreach ($pendingTransactions as $transaction){
            try{
                $balance->addTransaction($transaction);
            } catch (\Exception $exception){
                // The transaction is not valid - skip it!
                continue;
            }

            $res->transactions[] = $transaction;
            
            if (count($res->transactions) >= $this->blockValidator->getTransactionsLimit()){
                break;
            }
        }

        $res->transactions[] = $this->transactionFactory->buildCoinbaseForBlock($res);
        
        $res->data_hash = $this->blockHasher->getDataHash($res);
        return $res;
    }
}
