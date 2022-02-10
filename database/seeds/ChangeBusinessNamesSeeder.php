<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChangeBusinessNamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        
        $randomBusinessList = array(
            "Bistro Bazaar",
            "Bistro Captain", 
            "Bistroporium", 
            "Cuisine Street", 
            "Cuisine Wave", 
            "Deli Divine", 
            "Deli Feast", 
            "Eatery Hotspot", 
            "Eateryworks",
            "Feast Lounge", 
            "Feast Palace",
            "Grub Chef",
            "Grub lord", 
            "Kitchen Sensation", 
            "Kitchen Takeout",
            "Menu Feed", 
            "Menu Gusto", 
            "Munchies", 
            "Munch Grill", 
            "Munchtastic"
        );

        $randomName = $randomBusinessList[array_rand($randomBusinessList)];

        DB::table('business')->where('id', '<', '109')
        ->chunkById(100, function ($users, $randomName) {
            foreach ($users as $user) {
                DB::table('business')
                    ->where('id', $user->id)
                    ->update([
                        'name' => $randomName
                    ]);
            }
        });        
    }


}
