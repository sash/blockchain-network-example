<?php

use App\Jobs\UpdatePendingBalance;
use App\Node\Balance;
use App\NodeTransaction;
use App\Repository\BalanceRepository;
use App\Repository\BlockRepository;
use Illuminate\Database\Seeder;

class GenesisBlock extends Seeder
{
    /**
     * @var UpdatePendingBalance
     */
    private $updatePendingBalance;
    
    /**
     * GenesisBlock constructor.
     * @param UpdatePendingBalance $updatePendingBalance
     */
    function __construct(UpdatePendingBalance $updatePendingBalance)
    {
        $this->updatePendingBalance = $updatePendingBalance;
    }
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $initialBalance = [
            'be9c053812ca0cf8ae40aab3047f1b17e586765d' => 10000 * 1000000,
            'c06e8b1d745f50658be0a6e4bd6b01c94878a923' => 10000 * 1000000,
            '9a0bc19436ff653a7c631edc82451a684bccbbb2' => 10000 * 1000000,
            'b379a0f6378b612a46a346e8136ba3b9fb324218' => 10000 * 1000000,
            '626b5ce05e2b40812cf283fc45434e799f036d9c' => 10000 * 1000000,
            'f86d8b68d81bd1bb2637e4874c31c1d5bd3a0169' => 10000 * 1000000,
            'b0238c61a3bfe54bd7ae95aeaea25a3a942759e4' => 10000 * 1000000,
            'a163ad1c02203518eeae23f6348d8c1dc00dac25' => 10000 * 1000000,
            '6b2bc3ffcda1f096a3ac08fa38352505f57e543e' => 10000 * 1000000,
            '004ca2dd10fcf53ad30631e7d323aa80c9ecf317' => 10000 * 1000000,
        ];
        
        $repo = $this->container->make(BlockRepository::class);
        $genesis = $repo->newGenesisBlock($initialBalance);
        
        if (!$repo->getBlockWithHash($genesis->block_hash)){
            $genesis->save();
    
            $this->container->make(BlockRepository::class)->linkTransactions($genesis);
            $initialBalance[NodeTransaction::COINBASE_ADDRESS] = -1 * array_sum($initialBalance);
            $balance = new Balance($initialBalance, $this->container->make(BalanceRepository::class));
            $balance->saveForBlock($genesis);
            $this->updatePendingBalance->update();
        }
    }
}
