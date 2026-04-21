<?php

namespace Database\Seeders;

use App\Models\Vault;
use Illuminate\Database\Seeder;

class VaultSeeder extends Seeder
{
    public function run(): void
    {
        Vault::firstOrCreate(
            ['name' => 'الخزينة الرئيسية'],
            ['opening_balance' => 0, 'current_balance' => 0],
        );
    }
}
