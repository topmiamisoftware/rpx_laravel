<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class AlbumItemSeeder extends Seeder
{
    public function run()
    {
        
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    
        if(!$link) {die('Failed to connect to server: ' . mysqli_connect_error() . " Line Number: " . __LINE__ . " File Name: " . __FILE__);}
    
        $con = $link;

        $sql = "SELECT * FROM exe_album_items WHERE 1";

        $qry = mysqli_query($con, $sql);

        if($qry){
            while($album_item = $qry->fetch_array(MYSQLI_ASSOC)){

                DB::table('album_items')->insert([
                    'id' => $album_item['album_media_id'],
                    'album_owner_user_id' => $album_item['album_by'],
                    'media_owner_user_id' => $album_item['user_id'],
                    'album_id' => $album_item['album_id'],
                    'media_type' => $album_item['album_media_type'],  
                    'caption' => $album_item['album_item_caption'],  
                    'content' => $album_item['album_media_content'],  
                    'active' => $album_item['item_active'],                        
                    'created_at' => $album_item['item_created'],
                    'updated_at' => $album_item['item_updated']                               
                ]);

            }
        } else {   
            echo mysqli_error($con);
            die();
        } 
    }
}
