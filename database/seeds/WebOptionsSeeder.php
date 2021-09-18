<?php

use Illuminate\Database\Seeder;

class WebOptionsSeeder extends Seeder
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

        $sql = "SELECT * FROM exe_web_options WHERE 1";

        $qry = mysqli_query($con, $sql);

        if($qry){

            while($web_options = $qry->fetch_array(MYSQLI_ASSOC)){
                
                DB::table('web_options')->insert([
                    'id' => $web_options['options_id'],
                    'user_id' => $web_options['exe_user_id'],
                    'spotmee_bg' => $web_options['spotmee_bg'],
                    'spotmee_bg_date' => $web_options['spotmee_bg_date'],
                    'updated_at' => $web_options['last_updated'],
                    'bg_color' => $web_options['bg_color'],
                    'time_zone' => $web_options['exe_time_zone']                                             
                ]);

            }
            
        } else {   
            echo mysqli_error($con);
            die();
        }
        */
    }
}
