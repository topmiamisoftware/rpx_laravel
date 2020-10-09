<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_media', function (Blueprint $table) {
            $table->id();
            $table->string('type', 10);
            $table->string('content', 250);
            $table->unsignedBigInteger('stream_id')->references('streams')->on('id');
            $table->unsignedBigInteger('stream_post_id')->references('stream_posts')->on('id');
            $table->unsignedBigInteger('owner_user_id')->references('users')->on('id');
            $table->boolean('status');
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
        Schema::dropIfExists('extra_media');
    }
}
