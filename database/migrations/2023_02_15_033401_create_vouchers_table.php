<?php

use App\Domains\Voucher\Models\Voucher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  //creator
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->string('code', 100);
            $table->enum('type', Voucher::getTypes())->default(Voucher::TYPE_PERCENTAGE);
            $table->enum('number_times_used_type', Voucher::getNumberTimesUsedTypes())->default(Voucher::TYPE_TIMES);
            $table->unsignedInteger('number_times_used_value')->default(1);
            $table->unsignedTinyInteger('scope')->default(Voucher::ALL_SCOPE);
            $table->unsignedDecimal('discount', 17, 2);
            $table->unsignedDecimal('max_discount', 17, 2)->nullable();
            $table->unsignedInteger('count')->nullable();
            $table->boolean('status')->default(true);
            $table->date('expired_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['code', 'deleted_at'], 'vouchers_code_unique');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
}
