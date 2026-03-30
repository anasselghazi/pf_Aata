<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Categorie;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campagne>
 */
class CampagneFactory extends Factory
{
    public function definition(): array
    {
        $titre = fake()->sentence(3);

        return [
            'titre'=> $titre ,
            'slug' => Str::slug($titre),
            'description'=> fake()->paragraph(),
            'objectif_financier' => fake()->randomFloat(2, 1000, 50000),
            'montant_collecte' => 0,
            'statut' => fake()->randomElement(['en_attente', 'approuvee', 'rejetee', 'terminee']),

            'beneficiaire_id' => User::factory(), 
            'categorie_id' => Categorie::factory(),
        ];
    }
}