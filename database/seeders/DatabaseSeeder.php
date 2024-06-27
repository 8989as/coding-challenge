<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(IngredientSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(ProductIngredienteSeeder::class);
    }
}
