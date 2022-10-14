<?php

namespace Database\Factories;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bookmark>
 */
class BookmarkFactory extends Factory
{
    protected $model = Bookmark::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::first()->id ?? User::factory(),
            'url' => $this->faker->url,
            'title' => $this->faker->boolean(70)
                ? $this->faker->words(random_int(2, 5), true)
                : $this->faker->domainName,
            'description' => $this->faker->boolean(70) ? $this->faker->sentences(random_int(1, 3), true) : null,

        ];
    }
}
