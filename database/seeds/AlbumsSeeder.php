<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class AlbumsSeeder extends Seeder
{
    public function run()
    {
        /*
        $link = mysqli_connect(config('database.connections.mysql_spotbie_old.host'), 
                                config('database.connections.mysql_spotbie_old.username'), 
                                config('database.connections.mysql_spotbie_old.password'), 
                                config('database.connections.mysql_spotbie_old.database'));
        $con = $link;

        $sql = "SELECT * FROM exe_albums WHERE belongs_to = '1'";

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
        */
    }
}
