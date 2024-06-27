<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

class IngredientFactory extends Factory
{
    protected $model = Ingredient::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'stock' => $this->faker->numberBetween(100, 20000),
            'unit' => $this->faker->word,
            'reOrder_point' => $this->faker->numberBetween(50, 10000),
            'email_sent' => false,
        ];
    }
}
