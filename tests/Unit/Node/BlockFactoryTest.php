<?php
/**
 * Created by PhpStorm.
 * User: sash
 * Date: 18.6.2018
 * Time: 12:41
 */

namespace Tests\Unit\Validators;

use App\Crypto\BlockHasher;
use App\Node\BalanceFactory;
use App\Node\BlockFactory;
use App\Node\Difficulty;
use App\Node\TransactionFactory;
use App\NodeTransaction;
use App\Repository\BlockRepository;
use App\Repository\TransactionRepository;
use App\Validators\BlockValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var BlockValidator
     */
    protected $validator;
    /**
     * @var BlockRepository
     */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();
        $this->repository = app(BlockRepository::class);
    }

    /** @test */
    public function test_i_can_create_valid_block()
    {
        $diff = $this->getMockBuilder(BlockValidator::class)
                     ->disableOriginalConstructor()
                     ->setProxyTarget(app(BlockValidator::class))
                     ->getMock();

        $diff->method('getTransactionsLimit')->willReturn(3);

        $this->seed(\GenesisBlock::class);
        $parent = $this->repository->getGenesisBlock();

        $blockFactory = new BlockFactory(
            app(TransactionRepository::class),
            app(BlockRepository::class),
            app(BlockHasher::class),
            app(BalanceFactory::class),
            app(TransactionFactory::class),
            $diff
        );

        $txs = $this->seedWith3Transactions();

        //should not be included in the block because of transactions limit
        $txD = factory(NodeTransaction::class)->create([
            'senderAddress' => 'b379a0f6378b612a46a346e8136ba3b9fb324218',
            'value' => 700,
            'fee' => 50,
            'block_id' => null
        ]);

        $candidateBlock = $blockFactory->buildMostProfitableFromPending('miner-address');

        $this->assertEquals((new Difficulty)->minZeroesInHash(), $candidateBlock->difficulty);
        $this->assertEquals($parent->block_hash, $candidateBlock->previous_block_hash);
        $this->assertEquals($parent->index+1, $candidateBlock->index);
        $this->assertEquals('miner-address', $candidateBlock->mined_by_address);
        $this->assertEquals(app(BlockHasher::class)->getDataHash($candidateBlock), $candidateBlock->data_hash);

        $this->assertTrue($candidateBlock->transactions->contains($txs[0]));
        $this->assertTrue($candidateBlock->transactions->contains($txs[1]));
        $this->assertTrue($candidateBlock->transactions->contains($txs[2]));
        $this->assertFalse($candidateBlock->transactions->contains($txD));

        $candidateBlockCoinbaseTx = $candidateBlock->transactions->last();
        $this->assertEquals(BlockValidator::COINBASE_MINING_FEE + $txs->sum('fee'), $candidateBlockCoinbaseTx->value);
        $this->assertEquals(NodeTransaction::COINBASE_ADDRESS, $candidateBlockCoinbaseTx->senderAddress);
        $this->assertEquals('miner-address', $candidateBlockCoinbaseTx->receiverAddress);
    }

    private function seedWith3Transactions()
    {
        $txA = factory(NodeTransaction::class)->create([
            'senderAddress' => 'be9c053812ca0cf8ae40aab3047f1b17e586765d',
            'value' => 1000,
            'fee' => 100,
            'block_id' => null
        ]);

        $txB = factory(NodeTransaction::class)->create([
            'senderAddress' => 'c06e8b1d745f50658be0a6e4bd6b01c94878a923',
            'value' => 900,
            'fee' => 110,
            'block_id' => null
        ]);

        $txC = factory(NodeTransaction::class)->create([
            'senderAddress' => '9a0bc19436ff653a7c631edc82451a684bccbbb2',
            'value' => 800,
            'fee' => 120,
            'block_id' => null
        ]);

        return collect([$txA, $txB, $txC]);
    }
}
