<?php

namespace App\Jobs;

use App\Node\Difficulty;
use App\NodeBlock;
use App\NodePeer;
use App\Repository\BlockRepository;
use App\Validators\BlockValidator;
use App\Validators\TransactionValidator;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncChain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var NodePeer
     */
    private $peer;
    /**
     * @var BlockValidator
     */
    private $blockValidator;
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
     * Create a new job instance.
     *
     * @param NodePeer $peer
     * @param BlockValidator $blockValidator
     * @param BlockRepository $blockRepository
     */
    public function __construct(NodePeer $peer, BlockValidator $blockValidator, BlockRepository $blockRepository, Difficulty $difficulty, TransactionValidator $transactionValidator)
    {
        //
        $this->peer = $peer;
        $this->blockValidator = $blockValidator;
        $this->blockRepository = $blockRepository;
        $this->difficulty = $difficulty;
        $this->transactionValidator = $transactionValidator;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $candidateChain = $this->peer->getBlocks();
    
        $this->blockValidator->assertValidChain($candidateChain);
    
        if ($this->chainIsMoreDifficult($candidateChain)) {
            $this->blockRepository->updateWithChain(
                    $candidateChain,
                    function ($transactionHash, $sequence, $block) {
                        $this->loadTransactionFromPeer(
                                $this->peer,
                                $transactionHash,
                                $sequence,
                                $block
                        );
                    }
            );
        }
    
        $this->revalidateAndResequencePendingTransactions();
    }
    
    private function chainIsMoreDifficult($candidateChain)
    {
        $tobBlock = $this->blockRepository->getTopBlock();
        $candidateDifficulty = $this->difficulty->difficultyOfChain($candidateChain);
        $currentDifficulty = $tobBlock->cumulativeDifficulty;
        if ($candidateDifficulty > $currentDifficulty) {
            return true;
        } else {
            if ($candidateDifficulty < $currentDifficulty) {
                return false;
            } else {
                // Compare the hashes of the top blocks
                return last($candidateChain)->block_hash > $tobBlock->block_hash;
            }
        }
    }
    
    /**
     * @param NodePeer $peer
     * @param $transactionHash
     * @param $sequence
     * @param NodeBlock $block
     * @throws \Exception
     */
    private function loadTransactionFromPeer(NodePeer $peer, $transactionHash, $sequence, NodeBlock $block)
    {
        $transaction = $peer->getTransaction($transactionHash);
        $this->transactionValidator->assertValid($transaction);
        
        $transaction->sequence = $sequence;
        $transaction->block_id = $block->id;
        
        $transaction->save();
    }
    
    private function revalidateAndResequencePendingTransactions()
    {
        // TODO: Implement
    }
}
