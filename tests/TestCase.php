<?php

namespace Tests;

use App\NodeBlock;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createSequenceOfBlocks($amount)
    {
        $blocks = [];

        for($i=1;$i<=$amount;$i++)
        {
            $blocks[] = factory(NodeBlock::class)->create(['index' => $i]);
        }

        return collect($blocks);
    }
}
