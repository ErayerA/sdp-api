<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('passport:client',[
            '--no-interaction'=>true,
            '--name'=>'Mukellef Password Grant Client',
            '--password' => true
        ]);
    }
}
