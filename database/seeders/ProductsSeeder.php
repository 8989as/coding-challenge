<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Cheese Burger',
                'description' => 'A delicious beef burger with cheese and onions.',
                'price' => 200, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cheese Lovers Pizza',
                'description' => '4 kinds of cheese pizza with a crispy crust.',
                'price' => 299,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Onion Rings',
                'description' => 'Crispy onion rings, perfect as a snack or side dish.',
                'price' => 55,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'French Fries',
                'description' => 'Golden and crispy French fries, perfect as a snack or side dish.',
                'price' => 45,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert($products);
    }
}
