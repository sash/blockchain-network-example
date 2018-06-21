<?php

namespace App\Console\Commands\Examples;

use App\Crypto\PublicPrivateKeyPair;
use Elliptic\EC;
use Illuminate\Console\Command;

class PrivateKeyToAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blockchain:private-to-address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exercises: Consensus Algorithms, 1) Private Key to Address';

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
    public function handle()
    {
        // Generate
        $key = PublicPrivateKeyPair::generate();
        
        echo "Private Key: \t\t\t".($key->getPrivateKey()) . "\n";
        echo "Uncompressed public key: \t".$key->getPublicKey() . "\n";

        echo "Compressed public key: \t\t".$key->getCompressedPublicKey()."\n";
        
        echo "Address: \t\t\t".$key->getAddress() . "\n";
        
        // Decode
        echo "\n\n";
        $key = PublicPrivateKeyPair::fromPrivateKey('fe549dbcccfbd11e255f6037e1e640efaca0e19966ac77a592fdf06d295952a4');
    
        echo "Extracted public key: \t\t" . $key->getCompressedPublicKey() . "\n";
    
        echo "Extracted Address: \t\t" . $key->getAddress() . "\n";
    
    
    }
    
}
