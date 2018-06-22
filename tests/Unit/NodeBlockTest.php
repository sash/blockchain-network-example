<?php

namespace Tests\Unit;

use App\NodeBlock;
use App\NodeTransaction;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NodeBlockTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_consists_of_transactions_ordered_by_sequence()
    {
        $this->seed(\GenesisBlock::class);
        $block = factory(NodeBlock::class)->create();
        factory(NodeTransaction::class)->create(['sequence'=>3, 'block_id' => $block->id]);
        factory(NodeTransaction::class)->create(['sequence'=>1, 'block_id' => $block->id]);
        factory(NodeTransaction::class)->create(['sequence'=>2, 'block_id' => $block->id]);

        $transactions = $block->transactions;

        $this->assertEquals([1, 2, 3], $transactions->pluck('sequence')->toArray());
    }
}
