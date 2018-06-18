<?php
/**
 * Created by PhpStorm.
 * User: sash
 * Date: 18.6.2018
 * Time: 14:15
 */

namespace Tests\Unit\Node;

use App\Crypto\PublicPrivateKeyPair;
use App\Crypto\TransactionHasher;
use App\Exceptions\InvalidTransaction;
use App\Node\Balance;
use App\Node\BalanceFactory;
use App\NodeBalance;
use App\NodeTransaction;
use App\Repository\BalanceRepository;
use App\Repository\BlockRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceTest extends TestCase
{
    use RefreshDatabase;
    
    private function getTransaction($signer = null, $target_address=null){
        if (!$target_address){
            $target_address = str_repeat('0', 40);
        }
    
        if (!$signer){
            $signer = PublicPrivateKeyPair::generate();
        }
        
        /**
         * @var TransactionHasher $hasher
         */
        $hasher = $this->app->make(TransactionHasher::class);
        
        $transaction = new NodeTransaction();
        $transaction->value = 1000000;
        $transaction->fee = 10;
        $transaction->senderAddress = $signer->getAddress();
        $transaction->senderSequence = 0;
        $transaction->receiverAddress = $target_address;
        $transaction->data = '';
        $transaction->hash = $hasher->getHash($transaction);
        $transaction->signature = $signer->sign($transaction->hash);
        return $transaction;
    
    }
    
    public function testAddTransaction_InvalidSpend()
    {
    
        $this->seed(\GenesisBlock::class);
        /**
         * @var Balance $balance
         */
        $balance = $this->app->make(BalanceFactory::class)->forBlock(
                $this->app->make(BlockRepository::class)->getGenesisBlock()
        );
    
    
        $this->expectException(InvalidTransaction::class);
        $this->expectExceptionMessage("Not enough funds to carry out the transaction");
        $balance->addTransaction($this->getTransaction());
    }
    
    public function testAddTransaction_ValidSpend()
    {
        $this->seed(\GenesisBlock::class);
        $owner = PublicPrivateKeyPair::fromPrivateKey('0f9d3070204642bc8eb07b00a99ef38eebfec965733a3f70548ce99484fdfd99'); // The first coin owner
        $target = PublicPrivateKeyPair::generate();
        $genesis = $this->app->make(BlockRepository::class)->getGenesisBlock();
        
    
        /**
         * @var Balance $balance
         */
        $balance = $this->app->make(BalanceFactory::class)->forBlock(
              $genesis
        );
        
        
        $this->assertTrue($balance->addTransaction($this->getTransaction($owner, $target->getAddress())));
        $this->assertEquals(10000 * 1000000 - 1000000 - 10, $balance->getForAddress($owner->getAddress()));
        $this->assertEquals(1000000, $balance->getForAddress($target->getAddress()));
    }
//
//    public function testBuildTransactions(){
//        for ($i=0; $i<10; $i++){
//            $key = PublicPrivateKeyPair::generate();
//            echo "| {$key->getAddress()} | {$key->getPrivateKey()} |\n";
//        }
//    }
}
