<?php

    namespace App\Models;

    use App\Models\User;

    class PublicStats{

        public function getTotalUsers(){

            $users = User::count();
            echo $users;
            return;

        }

    }

?>