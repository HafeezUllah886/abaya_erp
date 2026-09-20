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
        \App\Models\RawMaterial::create([
            'name' => 'Black Nida Fabric',
            'unit' => 'meter',
            'cost_price' => 500,
        ]);

        \App\Models\products::create([
            'name' => 'Classic Black Abaya',
            'sku' => 'AB-BLK-01',
            'retail_price' => 5000,
        ]);
    }
}
