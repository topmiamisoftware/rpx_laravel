<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class StreamPostCommentSeeder extends Seeder
{
    public function run()
    {
        
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    
        if(!$link) {die('Failed to connect to server: ' . mysqli_connect_error() . " Line Number: " . __LINE__ . " File Name: " . __FILE__);}
    
        $con = $link;

        $sql = "SELECT * FROM exe_comments WHERE 1";

        $qry = mysqli_query($con, $sql);

        if($qry){
            while($stream_post_comment = $qry->fetch_array(MYSQLI_ASSOC)){

                DB::table('stream_post_comments')->insert([
                    'id' => $stream_post_comment['c_id'],
                    'user_id' => $stream_post_comment['user_id'],
                    'stream_post_id' => $stream_post_comment['stream_post_id'],
                    'comment' => $stream_post_comment['comment'], 
                    'status' => $stream_post_comment['comment_active'],
                    'created_at' => $stream_post_comment['comment_date'],
                    'updated_at' => $stream_post_comment['comment_date']                               
                ]);

            }
        } else {   
            echo mysqli_error($con);
            die();
        } 
    }
}
