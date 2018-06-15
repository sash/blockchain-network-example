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
        
        $repo = new BlockRepository();
        $genesis = $repo->getGenesisBlock();
        
        try{
            $repo->getBlockWithHash($genesis->block_hash);
        } catch (Exception $e){
            // missing from the repository
            $genesis->save();
        }
        
    }
}
