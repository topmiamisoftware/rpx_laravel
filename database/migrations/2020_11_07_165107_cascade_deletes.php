<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CascadeDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('spotbie_users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->onDelete('cascade')->change();
        });

        Schema::table('streams', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->onDelete('cascade')->change();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('to')->onDelete('cascade')->change();
            $table->unsignedBigInteger('by')->onDelete('cascade')->change();
        });

        Schema::table('stream_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('original_post_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('stream_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('user_id')->onDelete('cascade')->change();
        });

        Schema::table('stream_post_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('stream_post_id')->onDelete('cascade')->change();
        });

        Schema::table('stream_post_likes', function (Blueprint $table) {
            $table->softDeletes();
            $table->unsignedBigInteger('stream_post_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('like_by_user_id')->onDelete('cascade')->change();
        });

        Schema::table('friendship', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('peer_id')->onDelete('cascade')->change();
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->onDelete('cascade')->change();
        });

        Schema::table('album_items', function (Blueprint $table) {
            $table->unsignedBigInteger('album_owner_user_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('media_owner_user_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('album_id')->onDelete('cascade')->change();
        });

        Schema::table('album_item_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('album_item_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('album_id')->onDelete('cascade')->change();
        });

        Schema::table('album_item_likes', function (Blueprint $table) {
            $table->softDeletes();
            $table->unsignedBigInteger('user_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('album_media_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('album_id')->onDelete('cascade')->change();
        });

        Schema::table('extra_media', function (Blueprint $table) {
            $table->unsignedBigInteger('stream_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('stream_post_id')->onDelete('cascade')->change();
            $table->unsignedBigInteger('owner_user_id')->onDelete('cascade')->change();
        });

        Schema::table('web_options', function (Blueprint $table) {
            $table->softDeletes();
            $table->unsignedInteger('user_id')->onDelete('cascade')->change(); 
        });

        Schema::table('user_locations', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->onDelete('cascade')->change();
        });

        Schema::table('default_images', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->onDelete('cascade')->change();
        });

        Schema::table('contact_mes', function (Blueprint $table) {
            $table->softDeletes();
            $table->unsignedInteger('user_id')->onDelete('cascade')->change();
        });

        Schema::table('my_places', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->onDelete('cascade')->change();
        });

        Schema::table('my_favorites', function (Blueprint $table) {
            $table->softDeletes();
            $table->unsignedBigInteger('user_id')->onDelete('cascade')->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
    
}
