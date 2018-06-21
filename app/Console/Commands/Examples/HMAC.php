<?php

namespace App\Console\Commands\Examples;

use Illuminate\Console\Command;

class HMAC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blockchain:hmac';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exercises: Blockchain Cryptography. 2) Calculate HMAC';

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
          "HMAC: ".hash_hmac('sha512', 'blockchain', 'devcamp')
        );
    }
}
