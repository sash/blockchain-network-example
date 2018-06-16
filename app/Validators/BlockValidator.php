<?php

namespace App\Validators;

use App\Crypto\BlockHasher;
use App\Node\Difficulty;
use App\NodeBlock;
use App\NodeTransaction;
use App\Repository\BlockRepository;

class BlockValidator
{
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
    }
    
    /**
     * @param NodeBlock $block
     * @throws \Exception
     */
    private function assertGenesisIsValid(NodeBlock $block)
    {
        $this->blockHasher->updateHashes($block);
        if ($block->block_hash != $this->blockRepository->getGenesisBlock()->block_hash){
            throw new \InvalidArgumentException('The genesis block is different');
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
        if ($diff < Difficulty::CURRENT_MIN_DIFFICULTY){
            throw new \InvalidArgumentException('The block with index ' . $block->index . ' has proof of work with difficulty setting of '.$diff.' with minimum '.Difficulty::CURRENT_MIN_DIFFICULTY.' required');
        }
    }
    
    /**
     * @param $block
     * @throws \App\Exceptions\InvalidTransaction
     */
    private function assertTransactionsAreValid($block)
    {
        foreach ($block->transactions as $transaction){
            $this->transactionValidator->assertValid($transaction);
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
}