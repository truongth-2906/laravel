<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAmountToTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('amount_sender', 11)->nullable()->after('status');
            $table->decimal('amount_receiver', 11)->nullable()->after('amount_sender');
            $table->string('currency', 255)->nullable()->after('amount_receiver');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('amount_sender');
            $table->dropColumn('amount_receiver');
            $table->dropColumn('currency');
        });
    }
}
