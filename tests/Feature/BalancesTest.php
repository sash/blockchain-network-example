<?php

namespace Tests\Feature;

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
    public function it_can_return_balance_with_incoming_transactions()
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

    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function it_returns_proper_balance_when_we_have_incoming_and_outgoing_transactions()
    {
        $receiverAddress = PublicPrivateKeyPair::generate()->getAddress();
        $blocks = $this->createSequenceOfBlocks(2);

        //not mined transaction
        factory(NodeTransaction::class)->create([
            'receiverAddress' => $receiverAddress,
            'value' => 100,
            'block_id' => null
        ]);

        //receiving transaction for 500
        factory(NodeTransaction::class)->create([
            'receiverAddress' => $receiverAddress,
            'value' => 500,
            'block_id' => $blocks[0]->id
        ]);

        //spending transaction for 350
        factory(NodeTransaction::class)->create([
            'senderAddress' => $receiverAddress,
            'value' => 300,
            'fee' => 50,
            'block_id' => $blocks[1]->id
        ]);

        $this->get("/api/balance/{$receiverAddress}")
            ->assertStatus(200)
            ->assertExactJson([
                'confirmed' => 150,
                'unconfirmed' => 250
            ]);
    }

    /** @test */
    public function it_returns_empty_balance_when_there_are_no_transactions_for_this_address()
    {
        $otherAddress = PublicPrivateKeyPair::generate()->getAddress();
        $myAddress = PublicPrivateKeyPair::generate()->getAddress();

        $block = factory(NodeBlock::class)->create();

        //not mined transaction
        factory(NodeTransaction::class)->create([
            'receiverAddress' => $otherAddress,
            'value' => 100,
            'block_id' => null
        ]);

        //receiving transaction for 500
        factory(NodeTransaction::class)->create([
            'receiverAddress' => $otherAddress,
            'value' => 500,
            'block_id' => $block->id
        ]);

        //spending transaction for 350
        factory(NodeTransaction::class)->create([
            'senderAddress' => $otherAddress,
            'value' => 300,
            'fee' => 50,
            'block_id' => $block->id
        ]);

        $this->get("/api/balance/{$myAddress}")
            ->assertStatus(200)
            ->assertExactJson([
                'confirmed' => 0,
                'unconfirmed' => 0
            ]);
    }

    private function createSequenceOfBlocks($amount)
    {
        $blocks = [];

        for($i=0;$i<$amount;$i++)
        {
            $blocks[] = factory(NodeBlock::class)->create(['index' => $i]);
        }

        return $blocks;
    }
}
