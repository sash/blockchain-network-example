<?php

namespace Tests;

use App\NodeBlock;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

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
}
