<?php

namespace Tests\Unit\Validators;

use App\Crypto\PublicKey;
use App\Crypto\PublicPrivateKeyPair;
use App\Exceptions\InvalidTransaction;
use App\NodeTransaction;
use App\Validators\TransactionValidator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionValidatorTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @var NodeTransaction
     */
    protected $subject;
    
    protected $validator;
    
    protected function buildTransaction($setup=null){
    
        $keyPair = PublicPrivateKeyPair::generate();
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
                'from_id'  => $subject->senderSequence,
                'to'       => $subject->receiverAddress,
                'value'    => $subject->value,
                'fee'      => $subject->fee,
                'data'     => $subject->data,
                'timestamp' => $subject->timestamp,
        ];
    
        $signatureFor = json_encode($signatureFor);
        $hash = hash('sha256', $signatureFor);
    
        $signature = $keyPair->sign($hash);
        
        $subject->hash = $hash; // Valid
        $subject->signature = $signature; // Valid
        return $subject;
    }
    
    protected function setUp(){
        parent::setUp();
        
        $this->subject = $this->buildTransaction();
        
        $this->validator = $this->app->make(TransactionValidator::class);
        
    }
    /**
     * @test
     *
     */
    public function it_passed_if_valid()
    {
        /**
         * @var $validator TransactionValidator
         */
//        for ($i=0; $i<1000;$i++){
            $subject = $this->buildTransaction();
            $this->assertNull($this->validator->assertValid($subject));
//        }
    }
    
    /**
     * @throws \Exception
     * @test
     */
    public function regression_failing_signature(){
        $key = PublicPrivateKeyPair::fromPrivateKey('567162ed990cc673fd8033ea5c4e64b8de596029a3223e7766bd6fab13b7d204');
        $this->assertEquals('4e545c49179c1ec795318810a5401f0a75e3547fb104d0c5edde30b8e6d4e68b0', $key->getCompressedPublicKey());
        $toSign = '80bbbce52a3ea45602b005779f71a2e6e1830d3ffe4ca238befa2ad4c901d68f';
        $signature = $key->sign($toSign);
        
//        $this->assertEquals('a7724553534e62f4a68574cf5128a58e8bfc434332c0ee8f11537ec9ffc8f0b7464e152cba166d027c707bb2b16374b2b41db960921a1e29366c1ce296dfca01', $signature);
        $restored = PublicKey::fromSignature(new NodeTransaction(), $signature, $toSign);
        $this->assertEquals('4e545c49179c1ec795318810a5401f0a75e3547fb104d0c5edde30b8e6d4e68b0', $restored->getCompressedPublicKey());
//        202 = "1497dcfd3e0070769cd0afc048ff1e0a3245c7b1 / 4e545c49179c1ec795318810a5401f0a75e3547fb104d0c5edde30b8e6d4e68b0 / 567162ed990cc673fd8033ea5c4e64b8de596029a3223e7766bd6fab13b7d204"
//203 = "Signing: 80bbbce52a3ea45602b005779f71a2e6e1830d3ffe4ca238befa2ad4c901d68f with a7724553534e62f4a68574cf5128a58e8bfc434332c0ee8f11537ec9ffc8f0b7464e152cba166d027c707bb2b16374b2b41db960921a1e29366c1ce296dfca01"
    }
    
    /**
     * @test
     * @expectedException \App\Exceptions\InvalidTransaction
     */
    public function it_fails_if_fee_too_low(){
        $this->validator->assertValid($this->buildTransaction(function($t){
            $t->fee=9;
        }));
    }
    
    /**
     * @test
     * @expectedException \App\Exceptions\InvalidTransaction
     */
    public function it_fails_if_hash_is_wrong()
    {
        $subject = $this->buildTransaction();
        $subject->hash = str_repeat('9', 64);
        $this->validator->assertValid($subject);
    }
    
    /**
     * @test
     * @expectedException \App\Exceptions\InvalidTransaction
     */
    public function it_fails_if_signature_is_wrong()
    {
        $subject = $this->buildTransaction();
        $subject->signature = str_repeat('01', 130);
        $this->validator->assertValid($subject);
    }
}