<?php

namespace App\Validators;

use App\Crypto\BlockHasher;
use App\Node\Difficulty;
use App\NodeBalance;
use App\NodeBlock;
use App\NodeTransaction;
use App\Repository\BlockRepository;

class BlockValidator
{
    //TODO extract constants to config
    const COINBASE_MINING_FEE = 10000000;
    
    const TRANSACTIONS_LIMIT = 10;
    
    /**
     * @var BlockHasher
     */
    private $blockHasher;
    /**
     * @var BlockRepository
     */
    private $blockRepository;
    /**
     * @var Difficulty
     */
    private $difficulty;
    /**
     * @var TransactionValidator
     */
    private $transactionValidator;
    
    /**
     * BlockValidator constructor.
     * @param BlockHasher $blockHasher
     * @param BlockRepository $blockRepository
     * @param Difficulty $difficulty
     * @param TransactionValidator $transactionValidator
     */
    public function __construct(BlockHasher $blockHasher, BlockRepository $blockRepository, Difficulty $difficulty, TransactionValidator $transactionValidator)
    {
        $this->blockHasher = $blockHasher;
        $this->blockRepository = $blockRepository;
        $this->difficulty = $difficulty;
        $this->transactionValidator = $transactionValidator;
    }
    
    /**
     * @param NodeBlock[] $chain
     * @throws \Exception
     */
    public function assertValidChain($chain){
        foreach ($chain as $i => $block){
            if ($i == 0){
                $this->assertGenesisIsValid($block);
            } else {
                $this->assertValidBlock($block, $chain[$i-1]);
            }
        }
    }
    
    /**
     * @param NodeBlock $block
     * @param NodeBlock $parent
     *
     * @throws \App\Exceptions\InvalidTransaction
     */
    public function assertValidBlock(NodeBlock $block, NodeBlock $parent)
    {
        $this->assertBlockIsBasedOnParentHash($block, $parent);
        $this->assertBlockIndexIsSequential($block, $parent);
        $this->assertProofOfWorkMatchesTheRequirement($block);
        $this->assertTransactionsAreValid($block);
        $this->assertCoinbaseIsValid($block);
        $this->assertTransactionsLimitIsKept($block);
    }

    public function getTransactionsLimit()
    {
        return self::TRANSACTIONS_LIMIT;
    }
    
    /**
     * @param NodeBlock $block
     * @throws \Exception
     */
    private function assertGenesisIsValid(NodeBlock $block)
    {
        $this->blockHasher->updateHashes($block);
        if ($block->block_hash != $this->blockRepository->getGenesisBlock()->block_hash){
            throw new \InvalidArgumentException('The genesis block is different - '.$block->index.' with hash '.$block->block_hash.' : '.json_encode($block));
        }
    }
    
    private function assertBlockIsBasedOnParentHash(NodeBlock $block, NodeBlock $parent)
    {
        if ($block->previous_block_hash != $parent->block_hash) {
            throw new \InvalidArgumentException('The block with index '.$block->index.' is not based on the parent hash');
        }
    }
    
    private function assertBlockIndexIsSequential($block, $parent)
    {
        if ($block->index != $parent->index + 1) {
            throw new \InvalidArgumentException('The block with index ' . $block->index . ' is not in sequence to its parent block');
        }
    }
    
    private function assertProofOfWorkMatchesTheRequirement(NodeBlock $block)
    {
        $this->blockHasher->updateHashes($block);
        $diff = $this->difficulty->zeroesInHash($block->block_hash);
        if ($diff < $this->difficulty->minZeroesInHash()){
            throw new \InvalidArgumentException('The block with index ' . $block->index . ' and hash '.$block->block_hash.' has proof of work with difficulty of '.$diff.' with minimum '. $this->difficulty->minZeroesInHash().' required');
        }
    }
    
    /**
     * @param $block
     * @throws \App\Exceptions\InvalidTransaction
     */
    private function assertTransactionsAreValid($block)
    {
        foreach ($block->transactions as $transaction){
            $this->transactionValidator->assertValid($transaction, true);
        }
    }
    
    private function assertCoinbaseIsValid(NodeBlock $block)
    {
        $coinbaseExpected = self::COINBASE_MINING_FEE;
        $coinbaseActual = 0;
        foreach ($block->transactions as $transaction) {
            if ($transaction->isCoinbase) {
                $coinbaseActual += $transaction->value;
            } else {
                $coinbaseExpected += $transaction->fee;
            }
        }
            
        if ($coinbaseActual != $coinbaseExpected) {
            throw new \InvalidArgumentException('Block is not valid. The coinbase transaction(s) do not match the expected value: ' . json_encode(['actual'   => $coinbaseActual,
                                                                                                                                            'expected' => $coinbaseExpected
                    ]));
        }
    }
    
    private function assertTransactionsLimitIsKept(NodeBlock $block)
    {
        if (count($block->transactions) > self::TRANSACTIONS_LIMIT){
            throw new \InvalidArgumentException('Block is not valid. The block contains more transactions then allowed');
        }
    }
}
