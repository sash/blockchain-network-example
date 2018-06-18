<?php

namespace Tests\Feature;

use App\Crypto\PublicPrivateKeyPair;
use App\NodeBlock;
use App\NodeTransaction;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @test
     * @return void
     */
    public function it_can_get_the_last_block()
    {
        
        $this->createSequenceOfBlocks(3, function($attributes){
            return $attributes + ["block_hash" => "hash-for-block-".$attributes['index']];
        });
    
        $this->get("/api/blocks/last")
             ->assertStatus(200)
             ->assertJsonFragment([
                 'block_hash' => 'hash-for-block-3',
             ]);
    }
    
    /**
     *
     * @test
     * @return void
     */
    public function it_can_get_all_blocks()
    {
        $this->createSequenceOfBlocks(3, function ($attributes) {
            return $attributes + ["block_hash" => "hash-for-block-" . $attributes['index']];
        });
        
        $this->get("/api/blocks")
                ->assertStatus(200)
                ->assertJsonCount(4);
    }
    
}
