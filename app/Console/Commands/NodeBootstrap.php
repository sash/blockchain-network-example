<?php

namespace App\Console\Commands;

use App\Jobs\SyncChain;
use App\Node\Broadcast;
use App\Node\Difficulty;
use App\NodePeer;
use App\Repository\BlockRepository;
use App\Repository\PeerRepository;
use App\Validators\BlockValidator;
use App\Validators\TransactionValidator;
use Illuminate\Console\Command;
use SebastianBergmann\Diff\Diff;

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
    protected $description = 'Connect to other peers and sync the chain';

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
     * @return mixed
     */
    public function handle(PeerRepository $peerRepository, Broadcast $broadcast, BlockRepository $blockRepository, BlockValidator $blockValidator, Difficulty $difficulty, TransactionValidator $transactionValidator)
    {
        $peerRepository->clearPeers();
        
        foreach ($peerRepository->knownPeers() as $peer){
            // Fetch peers from it and enrich the peers pool
            collect($peer->getPeers())->map(function(NodePeer $peer){$peer->wasActive();});
            
        }
        
        $broadcast->newPeer($peerRepository->currentPeer());
        
        foreach ($peerRepository->allPeers() as $peer){
    
            if ($blockRepository->getBlockWithHash($peer->getLastBlockHash())) {
                continue; // Already have the chain
            }
    
            $sync = new SyncChain($peer, $blockValidator, $blockRepository, $difficulty, $transactionValidator);
            dispatch_now($sync);
    
        }
    }
}
