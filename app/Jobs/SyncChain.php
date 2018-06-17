<?php

namespace App\Jobs;

use App\Node\Difficulty;
use App\NodeBlock;
use App\NodePeer;
use App\Repository\BlockRepository;
use App\Repository\TransactionRepository;
use App\Validators\BlockValidator;
use App\Validators\TransactionValidator;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

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
     * @var TransactionRepository
     */
    private $transactionRepository;
    
    /**
     * Create a new job instance.
     *
     * @param NodePeer $peer
     * @param BlockValidator $blockValidator
     * @param BlockRepository $blockRepository
     * @param Difficulty $difficulty
     * @param TransactionValidator $transactionValidator
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(NodePeer $peer, BlockValidator $blockValidator, BlockRepository $blockRepository, Difficulty $difficulty, TransactionValidator $transactionValidator, TransactionRepository $transactionRepository)
    {
        //
        $this->peer = $peer;
        $this->blockValidator = $blockValidator;
        $this->blockRepository = $blockRepository;
        $this->difficulty = $difficulty;
        $this->transactionValidator = $transactionValidator;
        $this->transactionRepository = $transactionRepository;
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
            $this->blockRepository->updateWithChain($candidateChain);
        }
    
        $this->destroyPendingCoinbaseTransactions();
        $this->clearSequenceOfPendingTransactions();
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
    
    private function clearSequenceOfPendingTransactions()
    {
        DB::transaction(function(){
            foreach ($this->transactionRepository->pendingTransactions() as $transaction){
                $transaction->sequence = null;
                $transaction->save();
            }
        });
        
    }
    
    
    private function destroyPendingCoinbaseTransactions()
    {
        $this->transactionRepository->pendingTransactions()->coinbase()->delete();
    }
}
