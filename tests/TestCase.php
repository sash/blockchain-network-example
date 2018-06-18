<?php

namespace Tests;

use App\Crypto\PublicPrivateKeyPair;
use App\NodeBlock;
use App\NodeTransaction;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Carbon;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();

        TestResponse::macro('assertJsonHasErrors', function ($value) {
            return collect($this->json()['errors'])->has('name');
        });
    }

    protected function createSequenceOfBlocks($amount, callable $attributesDecorator = null)
    {
        $blocks = [];

        for($i=1;$i<=$amount;$i++)
        {
            $attributes = ['index' => $i];
            if ($attributesDecorator){
                $attributes = call_user_func($attributesDecorator, $attributes);
            }
            $blocks[] = factory(NodeBlock::class)->create($attributes);
        }

        return collect($blocks);
    }

    protected function buildTransaction($setup=null){

        //TODO this function is used on other place too, refactor
        
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
            'datetime' => $subject->timestamp,
        ];

        $hash = hash('sha256', json_encode($signatureFor));

        $signature = $keyPair->sign($hash);

        $signatureFor['hash'] = $hash; // Valid
        $signatureFor['signature'] = $signature; // Valid
        return $signatureFor;
    }


}
