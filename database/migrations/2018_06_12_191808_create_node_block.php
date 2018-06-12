<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodeBlock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_blocks', function (Blueprint $table) {
            $table->increments('id');
        
            $table->integer('index');
            $table->integer('difficulty');
            $table->string('mined_by_address', 40);
            $table->string('previous_block_hash', 64);
            
            $table->string('data_hash', 64); // hash of the data above + the hashes of the transactions in sequence
            
            $table->integer('nonce');
            $table->integer('timestamp');
            $table->string('block_hash', 64); // hash(data_hash+nonce+timestamp)
            
            $table->bigInteger('cumulativeDifficulty');
            
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('node_blocks');
    }
}
