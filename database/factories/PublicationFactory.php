<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Publication>
 */
class PublicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Smknstd\FakerPicsumImages\FakerPicsumImagesProvider($faker));
        return [
            'image' => $faker->imageUrl(width: 800, height: 600),
            'description' => $this->faker->paragraph(($this->faker->numberBetween(1, 10))),
            'user_id' => User::inRandomOrder()->first()->id,
            'likes' => $this->faker->numberBetween(1, 10000)
        ];
    }
}
