<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed Role
        $role = File::get("database/seeders/role.json");
        $role = json_decode($role, true);
        DB::table('role')->insert($role);

        // Seed Menu
        $menu = File::get("database/seeders/menu.json");
        $menu = json_decode($menu, true);
        DB::table('menu')->insert($menu);

        // Seed User
        $user = File::get("database/seeders/user.json");
        $user = json_decode($user, true);
        DB::table('user')->insert(array_map(function ($item) {
            $item['password'] = Hash::make($item['password']);
            return $item;
        }, $user));
    }
}
