<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodeBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_balances', function (Blueprint $table) {
            $table->increments('id');
            
            $table->bigInteger('balance');
            $table->string('address', 40)->index();
           
            $table->unsignedInteger('block_id')->nullable();

//
            $table->foreign('block_id')->references('id')->on('node_blocks')->onDelete('cascade'); // delete the balances saved for blocks that are deleted!
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('node_balances');
    }
}
