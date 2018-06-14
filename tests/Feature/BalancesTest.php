<?php

namespace Tests\Feature;

use App\Crypto\PublicKey;
use App\Crypto\PublicPrivateKeyPair;
use App\NodeBlock;
use App\NodeTransaction;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BalancesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function it_can_return_balance()
    {
        //public key
        $receiverAddress = PublicPrivateKeyPair::generate()->getAddress();

        //precond
        $transaction = factory(NodeTransaction::class)->states('pending')->create([
            'receiverAddress' => $receiverAddress
        ]);

        //action
        $response = $this->get("api/balances/{$receiverAddress}");

        //assert
        $response->assertStatus(200);
        var_dump($response->json());
    }
}
