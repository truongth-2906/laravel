<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryUseVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_use_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('saved_voucher_id');
            $table->unsignedBigInteger('transaction_id');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['saved_voucher_id', 'transaction_id', 'deleted_at'], 'vouchers_used_once_unique');
            $table->foreign('saved_voucher_id')->references('id')->on('saved_vouchers');
            $table->foreign('transaction_id')->references('id')->on('transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_use_vouchers');
    }
}
