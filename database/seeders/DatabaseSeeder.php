<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Setting::current();

        foreach (['Autores', 'Notícias', 'Usuários', 'Publicidade'] as $name) {
            Category::firstOrCreate(['slug' => \Illuminate\Support\Str::slug($name)], ['name' => $name]);
        }

        User::updateOrCreate(
            ['email' => 'admin@pigea.com'],
            [
                'name' => 'Administrador PIGEA',
                'role' => 'admin',
                'password' => 'admin123',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@pigea.com'],
            [
                'name' => 'Utilizador Demo',
                'role' => 'user',
                'password' => 'user123',
            ]
        );
    }
}
