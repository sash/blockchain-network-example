<?php

namespace Tests;

use App\Crypto\PublicPrivateKeyPair;
use App\Jobs\UpdateBlockBalance;
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
            return collect(@$this->json()['errors'])->has('name');
        });
    }

    protected function createSequenceOfBlocks($amount, callable $attributesDecorator = null)
    {
        $this->seed(\GenesisBlock::class);
    
        $blocks = [];
        

        for($i=1;$i<=$amount;$i++)
        {
            $attributes = ['index' => $i];
            if ($i > 1){
                $parent = $blocks[$i-2];
                $attributes['previous_block_hash'] = $parent->block_hash;
            }
            
            if ($attributesDecorator){
                $attributes = call_user_func($attributesDecorator, $attributes);
            }
            $blocks[] =$block= factory(NodeBlock::class)->create($attributes);
            
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
            'timestamp' => $subject->timestamp,
        ];

        $hash = hash('sha256', json_encode($signatureFor));

        $signature = $keyPair->sign($hash);

        $signatureFor['hash'] = $hash; // Valid
        $signatureFor['signature'] = $signature; // Valid
        return $signatureFor;
    }

    public function getPrivateKey($address){

        $keyPairs = [
            'be9c053812ca0cf8ae40aab3047f1b17e586765d' => '0f9d3070204642bc8eb07b00a99ef38eebfec965733a3f70548ce99484fdfd99',
            'c06e8b1d745f50658be0a6e4bd6b01c94878a923' => 'e5fcb644cb5ff2a34d8d479b2fc775c6e4f242ebd8f4eb146bf3985d968c67a5',
            '9a0bc19436ff653a7c631edc82451a684bccbbb2' => '1827f2551a5e6c64f4a601c569c3a092c8a1dd770246947ecc8d6f01b29db2db',
            'b379a0f6378b612a46a346e8136ba3b9fb324218' => 'b3cf4c12b7e41b138ce19af734e7f3856a58858ca1430fb0f0c086b4f644c476',
            '004ca2dd10fcf53ad30631e7d323aa80c9ecf317' => '600fea4a214cadb607e34ed0bb091297864cc12162f1e6d6f67a4c5efac06e05'
        ];

        return $keyPairs[$address];
    }
}
