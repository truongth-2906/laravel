<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            if (Schema::hasColumn('countries', 'region_id')) {
                $table->dropForeign(['region_id']);
                $table->dropColumn('region_id');
            }
        });
        Schema::dropIfExists('regions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('countries', function (Blueprint $table) {
            if (!Schema::hasColumn('countries', 'region_id')) {
                $table->unsignedBigInteger('region_id')->nullable()->after('id');

                $table->foreign('region_id')
                    ->references('id')
                    ->on('regions')
                    ->onDelete('cascade');
            }
        });
    }
}
