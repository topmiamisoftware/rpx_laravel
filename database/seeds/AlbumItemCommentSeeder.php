<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class AlbumItemCommentSeeder extends Seeder
{
    public function run()
    {
        /*
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    
        if(!$link) {die('Failed to connect to server: ' . mysqli_connect_error() . " Line Number: " . __LINE__ . " File Name: " . __FILE__);}
    
        $con = $link;

        $sql = "SELECT * FROM exe_album_media_comments WHERE 1";

        $qry = mysqli_query($con, $sql);

        if($qry){
            while($comment = $qry->fetch_array(MYSQLI_ASSOC)){

                DB::table('album_item_comments')->insert([
                    'id' => $comment['c_id'],
                    'user_id' => $comment['user_id'],
                    'album_item_id' => $comment['album_media_id'],
                    'album_id' => $comment['album_id'],
                    'comment' => $comment['comment'],
                    'created_at' => $comment['comment_date'],
                    'updated_at' => $comment['comment_date']                               
                ]);

            }
        } else {   
            echo mysqli_error($con);
            die();
        } 
        */
    }
}
