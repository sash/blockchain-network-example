<?php

namespace App\Console\Commands;

use App\Jobs\SyncChain;
use App\Node\Broadcast;
use App\Node\Difficulty;
use App\NodePeer;
use App\Repository\BlockRepository;
use App\Repository\PeerRepository;
use App\Repository\TransactionRepository;
use App\Validators\BlockValidator;
use App\Validators\TransactionValidator;
use Illuminate\Console\Command;

class NodeBootstrap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'node:bootstrap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connect to pre-defined peers, collect known peers and sync the chain';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @param PeerRepository $peerRepository
     * @param Broadcast $broadcast
     * @param BlockRepository $blockRepository
     * @param BlockValidator $blockValidator
     * @param Difficulty $difficulty
     * @param TransactionValidator $transactionValidator
     * @param TransactionRepository $transactionRepository
     * @return mixed
     * @throws \Exception
     */
    public function handle(PeerRepository $peerRepository, Broadcast $broadcast, BlockRepository $blockRepository, BlockValidator $blockValidator, Difficulty $difficulty, TransactionValidator $transactionValidator, TransactionRepository $transactionRepository)
    {
        $this->info("Resetting known peers");
        $peerRepository->clearPeers();
        
        foreach ($peerRepository->knownPeers() as $peer){
    
            $this->info("Fetch peers from ".$peer->host);
            // Fetch peers from it and enrich the peers pool
            collect($peer->client->getPeers())->each(function(NodePeer $peer){
    
                $this->info("Found new peer:" . $peer->host);
                $peer->wasActive();
            });
            $peer->wasActive();
        }
        $this->info("Announce self to all peers - ". $peerRepository->currentPeer()->host);
        $broadcast->newPeer($peerRepository->currentPeer());
        
        foreach ($peerRepository->allPeers() as $peer){
    
            $this->info("Syncing with peer: ".$peer->host.' ...');
            
            $topBlock = $peer->client->getLastBlock();
    
            if ($topBlock->chain_id != $blockRepository->getGenesisBlock()->block_hash) {
                continue; // The peer servers some other chain!
            }
            if ($blockRepository->getBlockWithHash($topBlock->block_hash)) {
                continue; // Already have the top block
            }
            if ($difficulty->AIsMoreDifficultThenB($blockRepository->getTopBlock(), $topBlock)){
                continue; // Already have a more difficult chain
            }
            
            $sync = new SyncChain($peer, $blockValidator, $blockRepository, $difficulty, $transactionValidator, $transactionRepository);
            dispatch_now($sync);
    
        }
    }
}
