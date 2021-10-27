<?php

namespace App\Helpers;

class UrlHelper
{

    public function __construct(){}

    public static function getServerUrl(){

        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        }
        else{
            $protocol = 'http';
        }
        
        return $protocol . "://" . $_SERVER['HTTP_HOST'];

    }


}