<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Shift;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shift::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_id' => function () {
                return Client::factory()->create();
            },

            'workspace_id' => function () {
                return Workspace::factory()->create();
            },

            'started_at' => null,
            'ended_at' => null,
            'amount_due' => null,
            'vat' => null,
            'total_amount_due' => null,
            'status' => null,
            'prepaid_duration' => null,
            'paid_at' => null,
            'is_reservation' => null,
        ];
    }
}
