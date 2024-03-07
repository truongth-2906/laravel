<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsAddedWebhookToUserEscrowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_escrows', function (Blueprint $table) {
            if (!Schema::hasColumn('user_escrows', 'is_added_webhook')) {
                $table->boolean('is_added_webhook')->default(false)->after('last_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_escrows', function (Blueprint $table) {
            if (Schema::hasColumn('user_escrows', 'is_added_webhook')) {
                $table->dropColumn('is_added_webhook');
            }
        });
    }
}
