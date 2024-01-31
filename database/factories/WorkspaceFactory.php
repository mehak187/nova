<?php

namespace Database\Factories;

use App\Models\Workspace;
use App\Models\WorkspaceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkspaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Workspace::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'qr_image' => 'test',
            'workspace_type_id' => function () {
                return WorkspaceType::factory()->create();
            },
            'qr_code' => 'test',
        ];
    }
}
