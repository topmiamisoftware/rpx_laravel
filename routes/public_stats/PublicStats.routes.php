<?php

    /* Public Stats Controllers */
    Route::get('public_stats/total_users', function(){

        $stats = new \App\PublicStats();
        $stats->getTotalUsers();

    });
