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
            'name' => 'Sada Gasa Abaya',
            'sku' => 'AB-SADA-GASA-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
        products::create([
            'name' => 'Kam Wala Color Abaya',
            'sku' => 'AB-KAM-WALA-COLOR-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
        products::create([
            'name' => 'Dantail Color Abaya',
            'sku' => 'AB-Dantail-COLOR-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
        products::create([
            'name' => 'Jacket Abaya',
            'sku' => 'AB-JKT-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
        products::create([
            'name' => 'Kokan Wedding Black Abaya',
            'sku' => 'AB-KOKAN-WEDDING-BLK-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
        products::create([
            'name' => 'Tatriz Hath Kam Black Abaya',
            'sku' => 'AB-TATRIZ-HATH-KAM-BLK-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
        products::create([
            'name' => 'Satan Black Abaya',
            'sku' => 'AB-SATAN-BLK-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
        products::create([
            'name' => 'Torr + Stone Black Abaya',
            'sku' => 'AB-TORR-STONE-BLK-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
        products::create([
            'name' => 'Sada Torr',
            'sku' => 'AB-SADA-TORR-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
        products::create([
            'name' => 'Mushajar Abaya',
            'sku' => 'AB-MUSH-JAR-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
        products::create([
            'name' => 'Torr + Stone Color Abaya',
            'sku' => 'AB-TORR-STONE-COLOR-01',
            'price' => 200,
            'type' => 'Ready Made',
        ]);
    }
}
