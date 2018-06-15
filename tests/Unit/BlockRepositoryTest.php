<?php

namespace Tests\Unit;

use App\Repository\BlockRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_return_the_top_block()
    {
        $blocks = $this->createSequenceOfBlocks(4);

        $topBlock = (new BlockRepository())->getTopBlock();

        $this->assertTrue($blocks->last()->is($topBlock));
    }

    /** @test */
    public function it_can_return_all_blocks_ordered_by_their_index_ascending()
    {
        $this->createSequenceOfBlocks(4);

        $actualBlocks = (new BlockRepository())->getAllBlocks();

        $this->assertEquals(
            [1,2,3,4],
            $actualBlocks->pluck('index')->toArray()
        );
    }
}
