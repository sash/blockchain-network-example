<?php

namespace App\Validators;

use App\Crypto\BlockHasher;
use App\Node\Difficulty;
use App\NodeBlock;
use App\Repository\BlockRepository;

class BlockValidator
{
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
     * BlockValidator constructor.
     * @param BlockHasher $blockHasher
     * @param BlockRepository $blockRepository
     * @param Difficulty $difficulty
     */
    public function __construct(BlockHasher $blockHasher, BlockRepository $blockRepository, Difficulty $difficulty)
    {
        $this->blockHasher = $blockHasher;
        $this->blockRepository = $blockRepository;
        $this->difficulty = $difficulty;
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
     */
    public function assertValidBlock(NodeBlock $block, NodeBlock $parent)
    {
        $this->assertBlockIsBasedOnParentHash($block, $parent);
        $this->assertBlockIndexIsSequential($block, $parent);
        $this->assertProofOfWorkMatchesTheRequirement($block);
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
        $diff = $this->difficulty->difficultyOfHash($block->block_hash);
        if ($diff < Difficulty::CURRENT_DIFFICULTY){
            throw new \InvalidArgumentException('The block with index ' . $block->index . ' has proof of work with difficulty setting of '.$diff.' with minimum '.Difficulty::CURRENT_DIFFICULTY.' required');
        }
    }
}