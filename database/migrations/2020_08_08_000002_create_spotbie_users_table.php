<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpotbieUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spotbie_users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->references('id')->on('users');
            $table->integer('user_type', false, true)->default('0');
            $table->string('default_picture', 100)->default(config('spotbie.default_images_path').'user.png');
            $table->string('first_name', 72);
            $table->string('last_name', 72);
            $table->unsignedInteger('stream_id', true)->unique();
            $table->timestamp('last_log_in')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('animal', 30)->default('Panda');
            $table->boolean('over_18')->default(true);
            $table->string('description', 500)->nullable();
            $table->timestamp('last_default_picture_upload')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('last_known_ip_address', 256)->nullable();
            $table->boolean('ghost_mode')->default(false);
            $table->boolean('privacy')->default(false);
            $table->boolean('ads')->default(false);
            $table->boolean('mature')->default(false);
            $table->string('phone_number', 35)->nullable()->default(null);
            $table->boolean('phone_is_confirmed')->default(false);
            $table->unsignedInteger('phone_confirm_attempts')->default(0);
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
        Schema::dropIfExists('spotbie_users');
    }
}
