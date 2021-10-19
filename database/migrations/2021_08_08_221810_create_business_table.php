<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTable extends Migration
{

    private $allowedCategories = ['Asian Fusion', 'Bagels', 'Bakery', 'Bar', 'Barbeque', 'Breakfast', 'British',
    'Brunch', 'Buffets', 'Burgers', 'Cajun/Creole', 'Caribbean', 'Coffee/Espresso', 'Country Food', 'Cuban',
    'Deli', 'Doughnuts', 'Family Fare', 'Fast Food', 'Fine Dining', 'Food Trucks', 'French', 'German',
    'Gluten-free', 'Greek', 'Happy Hour', 'Hot Dogs', 'Ice Cream', 'Indian', 'Irish', 'Italian',
    'Japanese', 'Latin American', 'Live Entertainment', 'Mediterranean', 'Mexican', 'Nouvelle', 'Pancakes/Waffles', 'Pizza',
    'Polish', 'Sandwiches', 'Seafood', 'Soul Food', 'Soup & Salad', 'Southern', 'Spanish',
    'Sports Bar', 'Steaks', 'Sushi', 'Tapas', 'Thai', 'Vegan Friendly', 'Vegetarian'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->references('id')->on('users')->id()->unique();
            $table->string('name', 100)->nullable(false);
            $table->string('description', 500)->nullable(false);
            $table->string('address', 100)->nullable(false);
            $table->json('categories')->nullable(false);
            $table->float('loc_x', 8, 6)->nullable();
            $table->float('loc_y', 8, 6)->nullable();                        
            $table->string('photo', 650)->nullable(false)->default('');  
            $table->boolean('is_verified')->nullable(false)->default(false);
            $table->string('qr_code_link', 135)->nullable(false);      
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
    }
}
