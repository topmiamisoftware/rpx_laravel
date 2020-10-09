<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('to')->references('id')->on('users');
            $table->unsignedBigInteger('by')->references('id')->on('users');
            $table->string('content', 1500);
            $table->boolean('extra_media');
            $table->boolean('status');
            $table->boolean('read');
            $table->float('loc_x', 8, 6);
            $table->float('loc_y', 8, 6);
            $table->timestamp('time_read');
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
        Schema::dropIfExists('messages');
    }
}
