<?php

namespace Database\Factories;

use App\Models\OpeningTime;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpeningTimeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OpeningTime::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'day_name' => $this->faker->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            'is_open' => true,
            'time_from' => $this->faker->time('H:i'),
            'time_to' => $this->faker->time('H:i'),
        ];
    }
}
