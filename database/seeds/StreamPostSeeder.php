<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class StreamPostSeeder extends Seeder
{
    public function run()
    {
        
        /*$link = mysqli_connect(config('database.connections.mysql_spotbie_old.host'), 
                                config('database.connections.mysql_spotbie_old.username'), 
                                config('database.connections.mysql_spotbie_old.password'), 
                                config('database.connections.mysql_spotbie_old.database'));
        $con = $link;

        $sql = "SELECT * FROM exe_stream_posts WHERE stream_by = '1'";

        $qry = mysqli_query($con, $sql);

        if($qry){

            while($stream_post = $qry->fetch_array(MYSQLI_ASSOC)){

                DB::table('stream_posts')->insert([
                    'id' => $stream_post['stream_post_id'],
                    'original_post_id' => $stream_post['original_post_id'],
                    'stream_id' => $stream_post['belongs_to_stream'],
                    'user_id' => $stream_post['stream_by'],
                    'stream_content' => $stream_post['stream_content'],  
                    'extra_media' => $stream_post['extra_media'],  
                    'status' => $stream_post['stream_post_status'],
                    'loc_x' => $stream_post['loc_x'],     
                    'loc_y' => $stream_post['loc_y'],
                    'created_at' =>  $stream_post['last_update'], 
                    'updated_at' => $stream_post['last_update']                               
                ]);

            }

        } else {   
            echo mysqli_error($con);
            die();
        }        
        */
    }
}
