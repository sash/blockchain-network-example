<?php

use App\Repository\BlockRepository;
use Illuminate\Database\Seeder;

class GenesisBlock extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $repo = $this->container->make(BlockRepository::class);
        $genesis = $repo->getGenesisBlock();
        
        if (!$repo->getBlockWithHash($genesis->block_hash)){
            $genesis->save();
        }
        
        
    }
}
