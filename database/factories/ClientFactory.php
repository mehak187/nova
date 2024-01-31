<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->email,
            'email_verified_at' => now(),
            'password' => $this->faker->password(6, 6),
            // 'phone' => '123',
            'purchased_minutes_table' => 0,
            'purchased_minutes_office' => 0,
            'included_minutes_table' => 0,
            'included_minutes_office' => 0,
            'has_subscription' => false,
            'is_resident' => false,
            'is_235' => false,
            'register_code' => null,
            'door_access_enabled' => false,
            'credit_alert_sent' => false,
        ];
    }
}
