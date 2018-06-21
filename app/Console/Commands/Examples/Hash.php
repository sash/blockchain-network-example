<?php

namespace App\Console\Commands\Examples;

use Illuminate\Console\Command;
use kornrunner\Keccak;

class Hash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blockchain:hash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exercises: Blockchain Cryptography. 1) Calculate Hashes';

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
      $this->getOutput()->writeln(
        "SHA 256: ".hash('sha256', 'blockchain')
      );
      $this->getOutput()->writeln(
        "SHA 384: " . hash('sha384', 'blockchain')
      );
      $this->getOutput()->writeln(
        "SHA 512: " . hash('sha512', 'blockchain')
      );
      $this->getOutput()->writeln(
        "SHA3 512: " . hash('sha3-512', 'blockchain')
      );
  
      $this->getOutput()->writeln(
        "Keccak 512: " . Keccak::hash('blockchain', 512)
      );
      $this->getOutput()->writeln(
        "whirlpool 512: " . hash('whirlpool', 'blockchain')
      );
        
    }
}
