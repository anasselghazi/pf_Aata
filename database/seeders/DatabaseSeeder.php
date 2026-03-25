<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Categorie;
use App\Models\Campagne;
use App\Models\Don;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. المشرف
        User::factory()->create([
            'name'     => 'Admin Ataa',
            'email'    => 'admin@ataa.com',
            'role'     => 'admin',
            'password' => bcrypt('password123'),
        ]);

        // 2. الفئات
        $categories = ['Santé', 'Éducation', 'Pauvreté', 'Environnement'];
        foreach ($categories as $cat) {
            Categorie::create(['libelle' => $cat]);
        }

        // 3. المتبرعون
        User::factory(5)->create(['role' => 'donateur']);

        // 4. المستفيدون — يجب إنشاؤهم قبل الحملات
        User::factory(3)->create(['role' => 'beneficiaire']);

        // 5. الحملات — الآن مضمون إيجاد مستفيد
        Campagne::factory(10)->create();

        // 6. التبرعات
        Don::factory(20)->create();
    }
}