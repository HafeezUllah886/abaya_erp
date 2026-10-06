<?php

namespace Database\Seeders;

use App\Models\products;
use Illuminate\Database\Seeder;

class productsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        products::create([
            'name' => 'Black Nida Fabric',
            'unit' => 'meter',
            'price' => 5,
            'type' => 'Raw Material',
        ]);

        products::create([
            'name' => 'Classic Black Abaya',
            'sku' => 'AB-BLK-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
    }
}
