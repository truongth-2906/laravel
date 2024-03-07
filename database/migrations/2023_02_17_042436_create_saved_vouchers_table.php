<?php

use App\Domains\Voucher\Models\SavedVoucher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saved_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('voucher_id');
            $table->unsignedTinyInteger('status')->default(SavedVoucher::STATUS_ACTIVATED);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'voucher_id', 'deleted_at'], 'saved_vouchers_once_unique');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('voucher_id')->references('id')->on('vouchers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('saved_vouchers');
    }
}
