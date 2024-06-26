<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductIngredienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredientProduct = [
            [
                'product_id' => 1,
                'ingredient_id' => 1,
                'amount' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'ingredient_id' => 2,
                'amount' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'ingredient_id' => 3,
                'amount' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Other product-ingredient relationships
        ];

        // Insert data into the ingredient_product table
        DB::table('product_ingredient')->insert($ingredientProduct);
    }
}
