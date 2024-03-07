<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('escrow_transaction_id')->index();
            $table->string('reference', 40)->index();
            $table->string('status', 32);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sender_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('receiver_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('job_id')
                ->references('id')
                ->on('jobs')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
