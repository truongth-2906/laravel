<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->unsignedBigInteger('region_id')->nullable()->after('id');
            $table->string('calling_code')->nullable()->after('code');

            $table->foreign('region_id')
                ->references('id')
                ->on('regions')
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
        Schema::table('countries', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn(['region_id', 'calling_code']);
        });
    }
}
