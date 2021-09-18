<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class StreamPostLikeSeeder extends Seeder
{
    public function run()
    {
        /*
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    
        if(!$link) {die('Failed to connect to server: ' . mysqli_connect_error() . " Line Number: " . __LINE__ . " File Name: " . __FILE__);}
    
        $con = $link;

        $sql = "SELECT * FROM exe_stream_post_likes WHERE 1";

        $qry = mysqli_query($con, $sql);

        if($qry){
            while($stream_post_like = $qry->fetch_array(MYSQLI_ASSOC)){

                DB::table('stream_post_likes')->insert([
                    'id' => $stream_post_like['like_id'],
                    'stream_post_id' => $stream_post_like['post_id'],
                    'like_by_user_id' => $stream_post_like['like_by'],
                    'created_at' => $stream_post_like['liked_date'],
                    'updated_at' => $stream_post_like['liked_date']                               
                ]);

            }
        } else {   
            echo mysqli_error($con);
            die();
        } 
        */
    }
}
