<?php

namespace Database\Factories;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Folder>
 */
class FolderFactory extends Factory
{
    protected $model = Folder::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::first()->id ?? User::factory(),
            'name' => ucwords($this->faker->words(random_int(2, 5), true)),
            'description' => random_int(0, 1) ? $this->faker->sentences(random_int(1, 2), true) : null,

        ];
    }
}
