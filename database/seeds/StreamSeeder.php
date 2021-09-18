<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

use Carbon\Carbon;

class StreamSeeder extends Seeder
{
    public function run()
    {
        /*
        $link = mysqli_connect(config('database.connections.mysql_spotbie_old.host'), 
                                config('database.connections.mysql_spotbie_old.username'), 
                                config('database.connections.mysql_spotbie_old.password'), 
                                config('database.connections.mysql_spotbie_old.database'));

        $con = $link;

        $sql = "SELECT * FROM exe_streams WHERE stream_by = '1'";

        $qry = mysqli_query($con, $sql);

        if($qry){
            while($stream = $qry->fetch_array(MYSQLI_ASSOC)){
                
                DB::table('streams')->insert([
                    'id' => $stream['stream_id'],
                    'user_id' => $stream['stream_by'],
                    'status' => $stream['stream_status'],   
                    'updated_at' =>  Carbon::now(),       
                    'created_at' =>  Carbon::now()                          
                ]);

            }
        } else {   
            echo mysqli_error($con);
            die();
        }
        */
    }
}
