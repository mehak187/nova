<?php

namespace Database\Seeders;

use App\Models\OpeningTime;
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
        if (OpeningTime::count() === 7) {
            return;
        }

        OpeningTime::create([
            'day_name' => 'monday',
            'is_open' => true,
            'time_from' => '08:00',
            'time_to' => '19:00',
        ]);

        OpeningTime::create([
            'day_name' => 'tuesday',
            'is_open' => true,
            'time_from' => '08:00',
            'time_to' => '19:00',
        ]);

        OpeningTime::create([
            'day_name' => 'wednesday',
            'is_open' => true,
            'time_from' => '08:00',
            'time_to' => '19:00',
        ]);

        OpeningTime::create([
            'day_name' => 'thursday',
            'is_open' => true,
            'time_from' => '08:00',
            'time_to' => '19:00',
        ]);

        OpeningTime::create([
            'day_name' => 'friday',
            'is_open' => true,
            'time_from' => '08:00',
            'time_to' => '19:00',
        ]);

        OpeningTime::create([
            'day_name' => 'saturday',
            'is_open' => false,
            'time_from' => null,
            'time_to' => null,
        ]);

        OpeningTime::create([
            'day_name' => 'sunday',
            'is_open' => false,
            'time_from' => null,
            'time_to' => null,
        ]);
        // \App\Models\User::factory(10)->create();
    }
}
