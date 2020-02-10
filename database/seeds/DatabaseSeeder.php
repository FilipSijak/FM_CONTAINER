<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/countries.sql';
        DB::unprepared(file_get_contents($path));

        $path = 'database/cities.sql';
        DB::unprepared(file_get_contents($path));

        $path = 'database/clubs.sql';
        DB::unprepared(file_get_contents($path));

        $path = 'database/stadiums.sql';
        DB::unprepared(file_get_contents($path));
    }
}
