<?php

namespace Tests\Feature;

use App\Crypto\PublicKey;
use App\Crypto\PublicPrivateKeyPair;
use App\NodeBlock;
use App\NodeTransaction;
use Tests\TestCase;
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
        $receiverAddress = PublicPrivateKeyPair::generate()->getAddress();

        $block = factory(NodeBlock::class)->create();

        factory(NodeTransaction::class)->create([
            'receiverAddress' => $receiverAddress,
            'value' => 100,
            'block_id' => null
        ]);

        factory(NodeTransaction::class)->create([
            'receiverAddress' => $receiverAddress,
            'value' => 500,
            'block_id' => $block->id
        ]);

        $this->get("/api/balance/{$receiverAddress}")
             ->assertStatus(200)
             ->assertExactJson([
                 'confirmed' => 500,
                 'unconfirmed' => 600
             ]);
    }
}
