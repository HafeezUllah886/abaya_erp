<?php

namespace Database\Seeders;

use App\Models\accounts;
use Illuminate\Database\Seeder;

class accountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        accounts::create(
            [
                'title' => 'Cash Account',
                'type' => 'Business',
                'category' => 'Cash',
            ]
        );

        accounts::create(
            [
                'title' => 'Daily Sale',
                'type' => 'Customer',

            ]
        );

        accounts::create(
            [
                'title' => 'Walk-In Supplier',
                'type' => 'Supplier',
            ]
        );

        accounts::create(
            [
                'title' => 'Test Tailor',
                'type' => 'Tailor',
            ]
        );

        accounts::create(
            [
                'title' => 'Test Investor',
                'type' => 'Investor',
            ]
        );
    }
}
