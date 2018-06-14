<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodeTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_transactions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('senderAddress', 40)->index();
            $table->string('receiverAddress', 40)->index();
            $table->unsignedInteger('senderSequence'); // prevents replay attacks
            $table->unsignedInteger('sequence'); // The order of the transactions in a block
            $table->bigInteger('value');
            $table->bigInteger('fee');
            $table->text('data');
            $table->string('hash', 64)->unique();
            $table->string('signature', 130);
//            $table->bigInteger('minedInBlockIndex')->nullable();
            $table->unsignedInteger('block_id')->nullable();
            $table->timestamps();
//
            $table->foreign('block_id')->references('id')->on('node_blocks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('node_transactions');
    }
}
