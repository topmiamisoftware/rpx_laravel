<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(StreamSeeder::class);
        //$this->call(StreamPostSeeder::class);
        //$this->call(AlbumsSeeder::class);
        //$this->call(AlbumItemSeeder::class); 
        //$this->call(ExtraMediaSeeder::class);
        $this->call(UserSeeder::class);
    }
}
