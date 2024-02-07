<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Sms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->default(DB::raw('(UUID())'));
            $table->text('body', 320);
            $table->string('to_phone', 35);
            $table->boolean('sent')->default(false);
            $table->unsignedFloat('price');
            $table->unsignedBigInteger('to_id')->references('id')->on('users');
            $table->unsignedBigInteger('from_id')->references('id')->on('users');
            $table->unsignedBigInteger('failed_job_id')->nullable()->references('id')->on('failed_jobs');
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
        Schema::dropIfExists('sms');
    }
}
