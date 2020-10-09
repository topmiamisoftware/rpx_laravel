<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stream_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_post_id')->references('id')->on('stream_posts')->nullable();
            $table->unsignedBigInteger('stream_id')->references('id')->on('streams');
            $table->unsignedBigInteger('user_id')->references('id')->on('users');
            $table->string('stream_content', 1500);
            $table->boolean('extra_media');
            $table->boolean('status');
            $table->float('loc_x', 8, 6);
            $table->float('loc_y', 8, 6);
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
        Schema::dropIfExists('stream_posts');
    }
}
