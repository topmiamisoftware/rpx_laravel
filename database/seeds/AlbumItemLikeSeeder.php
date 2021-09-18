<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class AlbumItemLikeSeeder extends Seeder
{
    public function run()
    {
        /*
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    
        if(!$link) {die('Failed to connect to server: ' . mysqli_connect_error() . " Line Number: " . __LINE__ . " File Name: " . __FILE__);}
    
        $con = $link;

        $sql = "SELECT * FROM exe_album_media_likes WHERE 1";

        $qry = mysqli_query($con, $sql);

        if($qry){
            while($like = $qry->fetch_array(MYSQLI_ASSOC)){
                
                DB::table('album_item_likes')->insert([
                    'id' => $like['like_id'],
                    'user_id' => $like['user_id'],
                    'album_media_id' => $like['album_media_id'],
                    'album_id' => $like['album_id'],
                    'created_at' => $like['like_date'],
                    'updated_at' => $like['like_date'],                                               
                ]);

            }
        } else {   
            echo mysqli_error($con);
            die();
        } 
        */
    }
}
