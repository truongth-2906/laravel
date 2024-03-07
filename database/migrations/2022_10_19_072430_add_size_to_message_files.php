<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeToMessageFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message_files', function (Blueprint $table) {
            $table->unsignedBigInteger('size')->nullable()->after('file')->comment('kilobyte');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('message_files', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
}
