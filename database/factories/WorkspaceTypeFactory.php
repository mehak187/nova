<?php

namespace Database\Factories;

use App\Models\WorkspaceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkspaceTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkspaceType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'base_price' => $this->faker->numberBetween(20, 30),
            'subscription_price' => $this->faker->numberBetween(10, 20),
            'minutes_type' => $this->faker->randomElement(['table', 'office']),
        ];
    }
}
