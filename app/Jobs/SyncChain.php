<?php

namespace App\Jobs;

use App\Exceptions\InvalidTransaction;
use App\Node\BalanceFactory;
use App\Node\Difficulty;
use App\NodeBlock;
use App\NodePeer;
use App\Repository\BalanceRepository;
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
     * @var UpdatePendingBalance
     */
    private $updatePendingBalance;
    
    /**
     * Create a new job instance.
     *
     * @param NodePeer $peer
     * @param BlockValidator $blockValidator
     * @param BlockRepository $blockRepository
     * @param Difficulty $difficulty
     * @param TransactionValidator $transactionValidator
     * @param TransactionRepository $transactionRepository
     * @param BalanceFactory $balanceFactory
     * @param UpdatePendingBalance $updatePendingBalance
     */
    public function __construct(NodePeer $peer, BlockValidator $blockValidator, BlockRepository $blockRepository, Difficulty $difficulty, TransactionValidator $transactionValidator, TransactionRepository $transactionRepository, UpdatePendingBalance $updatePendingBalance)
    {
        //
        $this->peer = $peer;
        $this->blockValidator = $blockValidator;
        $this->blockRepository = $blockRepository;
        $this->difficulty = $difficulty;
        $this->transactionValidator = $transactionValidator;
        $this->transactionRepository = $transactionRepository;
        $this->updatePendingBalance = $updatePendingBalance;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        error_log("SyncChain: Start from {$this->peer->host}");
        $candidateChain = $this->peer->client->getBlocks();
    
        $this->blockValidator->assertValidChain($candidateChain);
    
        error_log("SyncChain: Chain is valid");
        if ($this->chainIsMoreDifficult($candidateChain)) {
            DB::transaction(function () use ($candidateChain) {
                error_log("SyncChain: Chain is more difficult");
                $this->blockRepository->updateWithChain($candidateChain);
    
                error_log("SyncChain: Updated with new chain");
                $this->destroyPendingCoinbaseTransactions();
                error_log("SyncChain: Cleared dangling coinbase transactoins");
                $this->clearSequenceOfPendingTransactions();
                $this->updatePendingBalances();
                error_log("SyncChain: Updated pending balance");
            });
        } else {
            error_log("SyncChain: Chain is less difficult");
        }
    
        error_log("SyncChain: End from {$this->peer->host}");
    }
    
    private function chainIsMoreDifficult($candidateChain)
    {
        $tobBlock = $this->blockRepository->getTopBlock();
        $topBlockInCandidate = last($candidateChain);
    
        // We dont believe the chain provided and want to compute the cumulative difficulty ourselves
        $topBlockInCandidate->cumulativeDifficulty = $this->difficulty->difficultyOfChain($candidateChain);
        
        return $this->difficulty->AIsMoreDifficultThenB($topBlockInCandidate, $tobBlock);
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
    
    private function updatePendingBalances()
    {
        $this->updatePendingBalance->update();
    }
}
