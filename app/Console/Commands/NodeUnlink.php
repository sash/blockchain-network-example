<?php

namespace App\Console\Commands;

use App\Repository\PeerRepository;
use Illuminate\Console\Command;

class NodeUnlink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'node:unlink';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forget all peers for the node';
    /**
     * @var PeerRepository
     */
    private $repository;
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PeerRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->repository->clearPeers();
        $this->info("Cleared all known peers for " . $this->repository->currentPeer()->host);
    
    }
}
