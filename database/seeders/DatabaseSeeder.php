<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            DynamicMenuAndPermissionSeeder::class,
            MasterWilayahSeeder::class,
            MasterTurnamenSeeder::class,
            MasterJabatanAndPosisiSeeder::class,
            UserAccountsSeeder::class,
        ]);
    }
}
