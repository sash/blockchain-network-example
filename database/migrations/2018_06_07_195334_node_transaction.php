<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NodeTransaction extends Migration
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
            $table->bigInteger('value');
            $table->bigInteger('fee');
            $table->text('data');
            $table->string('hash', 40)->unique();
            $table->string('signature', 130);
            $table->bigInteger('minedInBlockIndex')->nullable();
            $table->boolean('transferSuccessful')->nullable();
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
        Schema::dropIfExists('node_transactions');
    }
}
