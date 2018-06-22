<?php

namespace App\Console\Commands;

use App\Crypto\PublicPrivateKeyPair;
use App\Faucet\QueueRepository;
use App\Node\TransactionFactory;
use Illuminate\Console\Command;

class FaucetDrip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'faucet:drip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform one transaction from the queue';

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
    public function handle(QueueRepository $repository, TransactionFactory $factory)
    {
        if ($item = $repository->getTop()){
            $key = PublicPrivateKeyPair::fromPrivateKey($_ENV['PRIVATE_KEY']);
    
            $client = new \App\Faucet\PeerClient($item->peer);
    
            $balance = $client->getBalance($key->getAddress());
            $this->info("Sending 1Fs to ".$item->address.'...');
    
            $client->postTransaction($factory->buildSpendTransaction($key, $balance['txs'], 1000000, 10, $item->address,
                    'Free monet from faucet'));
            $item->delete();
            $this->info("Done!");
        }
        
    }
}
