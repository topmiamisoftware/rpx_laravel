<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class AlbumsSeeder extends Seeder
{
    public function run()
    {
        
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    
        if(!$link) {die('Failed to connect to server: ' . mysqli_connect_error() . " Line Number: " . __LINE__ . " File Name: " . __FILE__);}
    
        $con = $link;

        $sql = "SELECT * FROM exe_albums WHERE 1";

        $qry = mysqli_query($con, $sql);

        if($qry){
            while($album = $qry->fetch_array(MYSQLI_ASSOC)){

                DB::table('albums')->insert([
                    'id' => $album['exe_album_id'],
                    'user_id' => $album['belongs_to'],
                    'name' => $album['exe_album_name'],
                    'description' => $album['exe_album_description'],
                    'privacy' => $album['album_privacy'],  
                    'cover' => $album['cover'],  
                    'created_at' => $album['album_created'],
                    'updated_at' => $album['album_updated']                               
                ]);

            }
        } else {   
            echo mysqli_error($con);
            die();
        } 
    }
}
