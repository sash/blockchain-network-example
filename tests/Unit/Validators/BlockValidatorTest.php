<?php
/**
 * Created by PhpStorm.
 * User: sash
 * Date: 18.6.2018
 * Time: 12:41
 */

namespace Tests\Unit\Validators;

use App\Crypto\BlockHasher;
use App\Crypto\PublicPrivateKeyPair;
use App\Crypto\TransactionHasher;
use App\Exceptions\InvalidTransaction;
use App\Http\Resources\NodeBlockResource;
use App\Node\Difficulty;
use App\NodeBlock;
use App\NodeTransaction;
use App\Repository\BlockRepository;
use App\Validators\BlockValidator;
use App\Validators\TransactionValidator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SebastianBergmann\Diff\Diff;
use Tests\TestCase;

class BlockValidatorTest extends TestCase
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
        
        $this->validator = $this->getValidator(0);
        $this->repository = app(BlockRepository::class);
        
    }
    protected function getValidator($difficulty){
        $diff = $this->getMockBuilder(Difficulty::class)->setProxyTarget(app(Difficulty::class))->getMock();
        $diff->method('minZeroesInHash')->willReturn($difficulty); // Lower the difficulty to make all valid;

//        $this->difficulty = $difficulty;
    
        $validator = new BlockValidator(
                app(BlockHasher::class),
                app(BlockRepository::class),
                $diff,
                app(TransactionValidator::class)
        );
        
        return $validator;
    }
    
    protected function getValidBlock(NodeBlock $parent, $difficulty=0)
    {
        $res = new NodeBlock();
        $res->index = $parent->index+1;
        $res->previous_block_hash = $parent->block_hash;
        $res->difficulty = $difficulty;
        $res->timestamp = Carbon::now()->timestamp;
        $res->chain_id = $this->repository->getGenesisBlock()->block_hash;
        $res->mined_by_address = str_repeat('0', 40);
        $res->nonce = 0;
        $coinbase=new NodeTransaction();
        $coinbase->value = 10000000;
        $coinbase->fee = 0;
        $coinbase->senderAddress = str_repeat(0, 40);
        $coinbase->senderSequence = 0;
        $coinbase->receiverAddress = str_repeat('0', 40);
        $coinbase->data = '';
        $coinbase->signature = str_repeat(0, 130);
        $res->transactions[] = $coinbase;
        return $res;
    }
    
    protected function getValidChain($difficulty){
        $chain = [];
        $chain[] = $this->repository->getGenesisBlock();
        $chain[] = $this->getValidBlock($chain[count($chain)-1], $difficulty);
        return $chain;
    }
    
    public function testAssertValidBlock_ValidEmptyBlock()
    {
        $this->seed(\GenesisBlock::class);
        $parent = $this->repository->getGenesisBlock();
        $test = $this->getValidBlock($parent);
        
        $this->assertNull($this->validator->assertValidBlock($test, $parent));
    }
    
    public function testAssertValidBlock_InvalidCoinbase()
    {
    
        $this->seed(\GenesisBlock::class);
        $parent = $this->repository->getGenesisBlock();
        $test = $this->getValidBlock($parent);
    
    
        $test->transactions[0]->value += 100; // Theft
        
    
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->assertValidBlock($test, $parent);
    }
    
    public function testAssertValidBlock_WrongIndex()
    {
    
        $this->seed(\GenesisBlock::class);
        $parent = $this->repository->getGenesisBlock();
        $test = $this->getValidBlock($parent);
        
        
        $test->index = 100; // Invalid
        
        
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->assertValidBlock($test, $parent);
    }
    
    public function testAssertValidBlock_WrongParent()
    {
    
        $this->seed(\GenesisBlock::class);
        $parent = $this->repository->getGenesisBlock();
        $test = $this->getValidBlock($parent);
        
        
        $test->previous_block_hash = str_repeat('0', 64); // Invalid
        
        
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->assertValidBlock($test, $parent);
    }
    
    public function testAssertValidBlock_InvalidTransaction()
    {
    
        $this->seed(\GenesisBlock::class);
        $parent = $this->repository->getGenesisBlock();
        $test = $this->getValidBlock($parent);
        
        
        $transaction = new NodeTransaction();
        $transaction->value = 10000000;
        $transaction->fee = 0;
        $transaction->senderAddress = str_repeat('1', 40);
        $transaction->senderSequence = 0;
        $transaction->receiverAddress = str_repeat('2', 40);
        $transaction->data = '';
        $transaction->signature = str_repeat(0, 130);
        
        $test->transactions[] = $transaction;
        
        
        $this->expectException(\Exception::class);
        $this->validator->assertValidBlock($test, $parent);
    }
    
    public function testAssertValidBlock_InvalidDifficulty()
    {
    
        $this->seed(\GenesisBlock::class);
        $parent = $this->repository->getGenesisBlock();
        $test = $this->getValidBlock($parent, 0);
        
        $validator = $this->getValidator(4);
        $this->expectException(\Exception::class);
        $validator->assertValidBlock($test, $parent);
    }
    
    public function testAssertValidChain_Valid()
    {
    
        $this->seed(\GenesisBlock::class);
        $test = $this->getValidChain(0);
        $this->assertNull($this->validator->assertValidChain($test));
    }
    
    public function testAssertValidChain_DifferentGenesis()
    {
        $this->seed(\GenesisBlock::class);
        $test = [];
        $test[] = $this->repository->newGenesisBlock([], Carbon::now()->timestamp); // Alternative genesis
        $test[] = $this->getValidBlock($test[0]);
        
        $this->expectException(\Exception::class);
        $this->validator->assertValidChain($test);
    }
    
    
}
