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
     * BlockFactory constructor.
     * @param TransactionRepository $transactionRepository
     * @param BlockRepository $blockRepository
     * @param BlockHasher $blockHasher
     * @param BalanceFactory $balanceFactory
     * @param TransactionFactory $transactionFactory
     */
    function __construct(TransactionRepository $transactionRepository, BlockRepository $blockRepository, BlockHasher $blockHasher, BalanceFactory $balanceFactory, TransactionFactory $transactionFactory)
    {
        $this->transactionRepository = $transactionRepository;
        $this->blockRepository = $blockRepository;
        $this->blockHasher = $blockHasher;
        $this->balanceFactory = $balanceFactory;
        $this->transactionFactory = $transactionFactory;
    }
    
    function buildMostProfitableFromPending($miner_address): NodeBlock
    {
        $parent = $this->blockRepository->getTopBlock();
        
        $res = new NodeBlock();
        $res->difficulty = Difficulty::CURRENT_MIN_DIFFICULTY;
        $res->previous_block_hash = $parent->block_hash;
        $res->index = $parent->index + 1;
        $res->mined_by_address = $miner_address;
        
        $balance = $this->balanceFactory->forCurrentBlock($parent);
        
        foreach ($this->transactionRepository->pendingTransactions()->orderBy('fee', 'desc') as $transaction){
            try{
                $balance->addTransaction($transaction);
            } catch (\Exception $exception){
                // The transaction is not valid - skip it!
                continue;
            }
            
            $res->transactions[] = $transaction;
            
            if (count($res->transactions) >= BlockValidator::TRANSACTIONS_LIMIT){
                break;
            }
        }
        
        $res->transactions[] = $this->transactionFactory->buildCoinbaseForBlock($res);
        
        $res->data_hash = $this->blockHasher->getDataHash($res);
        return $res;
    }
}