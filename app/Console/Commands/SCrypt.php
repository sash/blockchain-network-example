<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SCrypt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blockchain:scrypt';

    /**
     * The console command description.
     *
     * @var string
     */
  protected $description = 'Exercises: Blockchain Cryptography. 3) Derive Key by Password using SCrypt';

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
      echo scrypt('p@$$w0rd~3', '7b07a2977a473e84fc30d463a2333bcfea6cb3400b16bec4e17fe981c925ba4f',
        16384, 16, 1, 32);
    }
}
