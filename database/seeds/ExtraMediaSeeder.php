<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class ExtraMediaSeeder extends Seeder
{
    public function run()
    {
        
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    
        if(!$link) {die('Failed to connect to server: ' . mysqli_connect_error() . " Line Number: " . __LINE__ . " File Name: " . __FILE__);}
    
        $con = $link;

        $sql = "SELECT * FROM exe_extra_media WHERE 1";

        $qry = mysqli_query($con, $sql);

        if($qry){

            while($stream_post = $qry->fetch_array(MYSQLI_ASSOC)){

                DB::table('extra_media')->insert([
                    'id' => $stream_post['extra_media_id'],
                    'type' => $stream_post['extra_media_type'],
                    'content' => $stream_post['extra_media_content'],
                    'stream_id' => $stream_post['belongs_to_stream'],
                    'stream_post_id' => $stream_post['belongs_to_stream_post'],  
                    'owner_user_id' => $stream_post['extra_media_by'],  
                    'status' => $stream_post['post_status'],  
                    'created_at' => $stream_post['post_updated'],
                    'updated_at' => $stream_post['post_updated']                               
                ]);

            }
        } else {   
            echo mysqli_error($con);
            die();
        } 
    }
}
