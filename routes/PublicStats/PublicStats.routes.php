<?php

use Illuminate\Support\Facades\Route;



Route::get('total-users', function(){

    $stats = new \App\Models\PublicStats();
    $stats->getTotalUsers();

});
