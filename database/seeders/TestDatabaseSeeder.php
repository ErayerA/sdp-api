<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //

        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'mukellef@mukellef.co',
        ]);

    }
}
