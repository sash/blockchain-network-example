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
        
        $this->assertTrue($this->validator->assertValid($this->subject));
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