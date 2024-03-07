<?php

use App\Domains\Auth\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', [User::TYPE_ADMIN, User::TYPE_FREELANCER, User::TYPE_EMPLOYER])->default(User::TYPE_EMPLOYER);
            $table->string('name');
            $table->string('firstname', 30)->nullable();
            $table->string('lastname', 30)->nullable();
            $table->string('username', 30)->unique()->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('work', 30)->nullable();
            $table->integer('hours')->nullable();
            $table->unsignedBigInteger('timezone_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('experience_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('sector_id')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->unsignedTinyInteger('active')->default(1);
            $table->unsignedTinyInteger('available')->default(1);
            $table->string('timezone')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->boolean('to_be_logged_out')->default(false);
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->rememberToken();

            $table->foreign('timezone_id')
                ->references('id')
                ->on('timezones')
                ->onDelete('cascade');

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('cascade');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            $table->foreign('experience_id')
                ->references('id')
                ->on('experiences')
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
