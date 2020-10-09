<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlbumItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('album_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('album_owner_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('media_owner_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('album_id')->references('id')->on('albums');
            $table->float('loc_x', 8, 6)->nullable();
            $table->float('loc_y', 8, 6)->nullable();
            $table->string('media_type', 10);
            $table->string('caption', 300)->nullable();
            $table->string('content', 250);
            $table->boolean('active');
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
        Schema::dropIfExists('album_items');
    }
}
