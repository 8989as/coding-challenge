<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            [
                'name' => 'Beef',
                'stock' => 20000,
                'unit' => 'grams',
                'reOrder_point' => 10000,
                'email_sent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cheese',
                'stock' => 5000,
                'unit' => 'grams',
                'reOrder_point' => 2500,
                'email_sent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Onion',
                'stock' => 1000,
                'unit' => 'grams',
                'reOrder_point' => 500,
                'email_sent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tomato',
                'stock' => 1000,
                'unit' => 'grams',
                'reOrder_point' => 500,
                'email_sent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Potato',
                'stock' => 2000,
                'unit' => 'grams',
                'reOrder_point' => 1000,
                'email_sent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('ingredients')->insert($ingredients);
    }
}
