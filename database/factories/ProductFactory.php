<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'category' => $this->faker->randomElement([
                'Elektronik',
                'Fashion',
                'Rumah Tangga',
                'Olahraga',
                'Kecantikan',
            ]),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 50000, 5000000),
            'stock' => $this->faker->numberBetween(0, 200),
            'image' => null,
            'is_active' => $this->faker->boolean(85),
        ];
    }
}
