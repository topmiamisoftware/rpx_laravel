<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class ExtraMediaSeeder extends Seeder
{
    public function run()
    {
        /*
        $link = mysqli_connect(config('database.connections.mysql_spotbie_old.host'), 
                                config('database.connections.mysql_spotbie_old.username'), 
                                config('database.connections.mysql_spotbie_old.password'), 
                                config('database.connections.mysql_spotbie_old.database'));
        $con = $link;

        $sql = "SELECT * FROM exe_extra_media WHERE extra_media_by = '1'";

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
        */ 
    }
}
