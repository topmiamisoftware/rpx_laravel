<?php

    namespace App;

    use App\User;

    class PublicStats{

        public function getTotalUsers(){

            $users = User::count();
            echo $users;
            return;

        }

    }

?>