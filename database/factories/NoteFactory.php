<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory {
    public function definition(): array {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'title'   => fake()->realText(15),
            'body'    => fake()->realText(1000),
        ];
    }
}

