<?php

namespace Tests\Feature;

use App\Crypto\PublicPrivateKeyPair;
use App\Http\Resources\NodeBlockResource;
use App\Node\BlockFactory;
use App\Node\Difficulty;
use App\NodeBlock;
use App\NodeTransaction;
use App\Repository\BalanceRepository;
use App\Repository\BlockRepository;
use App\Validators\BlockValidator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MinerTest extends TestCase
{
    use RefreshDatabase;

    protected function buildTransactionForKnownAddress($address,$setup=null){

        $keyPair = PublicPrivateKeyPair::fromPrivateKey($this->getPrivateKey($address));
        $keyPair->getAddress();

        $datetime = Carbon::create(2012, 1, 1, 0, 0, 0, 'Europe/Sofia');

        $subject = new NodeTransaction();
        $subject->sequence = 0;
        $subject->senderAddress = $keyPair->getAddress();
        $subject->senderSequence = 1;
        $subject->receiverAddress = str_repeat('0', 40);
        $subject->timestamp = $datetime->timestamp;
        $subject->value = 100;
        $subject->fee = 10;
        $subject->data = '';

        if ($setup){
            call_user_func($setup, $subject);
        }

        $signatureFor = [
            'from'     => $subject->senderAddress,
            'from_id'  => "$subject->senderSequence",
            'to'       => $subject->receiverAddress,
            'value'    => $subject->value,
            'fee'      => $subject->fee,
            'data'     => $subject->data,
            'timestamp' => "$subject->timestamp"
        ];

        $signatureFor = json_encode($signatureFor);
        $hash = hash('sha256', $signatureFor);
        $signature = $keyPair->sign($hash);

        $subject->hash = $hash; // Valid
        $subject->signature = $signature; // Valid
        return $subject;
    }

    /** @test */
    public function it_can_post_a_valid_block()
    {
        config()->set('settings.current_min_difficulty',0);
        $this->seed(\GenesisBlock::class);

        $txA = $this->buildTransactionForKnownAddress('be9c053812ca0cf8ae40aab3047f1b17e586765d',function($t){
            $t->senderAddress = 'be9c053812ca0cf8ae40aab3047f1b17e586765d';
            $t->receiverAddress = 'b379a0f6378b612a46a346e8136ba3b9fb324218';
            $t->value = "1000";
            $t->fee="100";
            $t->block_id = null;
        });
        $txA->save();

        $txB = $this->buildTransactionForKnownAddress('c06e8b1d745f50658be0a6e4bd6b01c94878a923',function($t){
            $t->senderAddress = 'c06e8b1d745f50658be0a6e4bd6b01c94878a923';
            $t->receiverAddress = 'b379a0f6378b612a46a346e8136ba3b9fb324218';
            $t->value = "900";
            $t->fee= "110";
            $t->block_id = null;
        });
        $txB->save();

        $txC = $this->buildTransactionForKnownAddress('9a0bc19436ff653a7c631edc82451a684bccbbb2',function($t){
            $t->senderAddress = '9a0bc19436ff653a7c631edc82451a684bccbbb2';
            $t->receiverAddress = 'b379a0f6378b612a46a346e8136ba3b9fb324218';
            $t->value = "800";
            $t->fee="120";
            $t->block_id = null;
        });
        $txC->save();

        $block = app(BlockFactory::class)->buildMostProfitableFromPending('004ca2dd10fcf53ad30631e7d323aa80c9ecf317');

        $block->difficulty = 0;
        $block->nonce = 0;
        $block->timestamp = Carbon::now()->timestamp;
        $block->chain = app(BlockRepository::class)->getGenesisBlock()->block_hash;
        $block->cumulativeDifficulty = 0;

        // 004ca2dd10fcf53ad30631e7d323aa80c9ecf317 miner address
        // b379a0f6378b612a46a346e8136ba3b9fb324218 should have received 1000+900+800=2700 coins
        $response = $this->postJson('/api/miner/job',[
           'block' =>  (new NodeBlockResource($block))->toArray($block)
        ])->assertStatus(201);

        $topBlock = app(BlockRepository::class)->getTopBlock();
        $genesisBlock = app(BlockRepository::class)->getGenesisBlock();

        $this->assertEquals(1, $topBlock->index);
        $this->assertEquals($genesisBlock->block_hash, $topBlock->previous_block_hash);

        $balanceRepository = app(BalanceRepository::class);
        $topBlockBalance = $balanceRepository->getBalanceForAddressOfBlock('b379a0f6378b612a46a346e8136ba3b9fb324218', $topBlock);
        $previousBlockBalance = $balanceRepository->getBalanceForAddressOfBlock('b379a0f6378b612a46a346e8136ba3b9fb324218', $genesisBlock);

        $this->assertEquals(
            2700,
            $topBlockBalance - $previousBlockBalance
        );

        $topBlockBalance = $balanceRepository->getBalanceForAddressOfBlock('be9c053812ca0cf8ae40aab3047f1b17e586765d', $topBlock);
        $previousBlockBalance = $balanceRepository->getBalanceForAddressOfBlock('be9c053812ca0cf8ae40aab3047f1b17e586765d', $genesisBlock);
        $this->assertEquals(
            $txA->value + $txA->fee, //1100
            abs($previousBlockBalance - $topBlockBalance)
        );

        $topBlockBalance = $balanceRepository->getBalanceForAddressOfBlock('c06e8b1d745f50658be0a6e4bd6b01c94878a923', $topBlock);
        $previousBlockBalance = $balanceRepository->getBalanceForAddressOfBlock('c06e8b1d745f50658be0a6e4bd6b01c94878a923', $genesisBlock);
        $this->assertEquals(
            $txB->value + $txB->fee, //1010
            abs($previousBlockBalance - $topBlockBalance)
        );

        $topBlockBalance = $balanceRepository->getBalanceForAddressOfBlock('9a0bc19436ff653a7c631edc82451a684bccbbb2', $topBlock);
        $previousBlockBalance = $balanceRepository->getBalanceForAddressOfBlock('9a0bc19436ff653a7c631edc82451a684bccbbb2', $genesisBlock);
        $this->assertEquals(
            $txC->value + $txC->fee, //920
            abs($previousBlockBalance - $topBlockBalance)
        );

        $minerTopBlockBalance = $balanceRepository->getBalanceForAddressOfBlock('004ca2dd10fcf53ad30631e7d323aa80c9ecf317', $topBlock);
        $minerPreviousBlockBalance = $balanceRepository->getBalanceForAddressOfBlock('004ca2dd10fcf53ad30631e7d323aa80c9ecf317', $genesisBlock);
        $this->assertEquals(
            BlockValidator::COINBASE_MINING_FEE + $txA->fee + $txB->fee + $txC->fee, //920
            abs($minerPreviousBlockBalance - $minerTopBlockBalance)
        );

        $this->assertEquals(2, $txA->fresh()->block_id);
        $this->assertEquals(2, $txB->fresh()->block_id);
        $this->assertEquals(2, $txC->fresh()->block_id);
    }

    /** @test */
    public function it_returns_last_block_hash()
    {
        $this->seed(\GenesisBlock::class);

        $response = $this->getJson('/api/miner/last-block-hash')->assertStatus(200);

        $genesisBlock = $genesisBlock = app(BlockRepository::class)->getGenesisBlock();

        $this->assertEquals($genesisBlock->block_hash, $response->json()['hash']);
    }
}
