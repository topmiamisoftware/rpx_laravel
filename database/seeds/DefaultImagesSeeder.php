<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class DefaultImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    
        if(!$link) {die('Failed to connect to server: ' . mysqli_connect_error() . " Line Number: " . __LINE__ . " File Name: " . __FILE__);}
    
        $con = $link;

        $sql = "SELECT * FROM user_default_pictures WHERE 1";

        $qry = mysqli_query($con, $sql);

        if($qry){
            while($default = $qry->fetch_array(MYSQLI_ASSOC)){
                
                DB::table('default_images')->insert([
                    'id' => $default['id'],
                    'user_id' => $default['user_id'],
                    'default_image_url' => $default['default_image_url'],
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
