<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Categorie;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campagne>
 */
class CampagneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titre'               => fake()->sentence(3),
            'description'         => fake()->paragraph(),
            'objectif_financier'  => fake()->randomFloat(2, 1000, 50000),
            'montant_collecte'    => 0,
            'statut'              => 'en_attente',

            'beneficiaire_id' => User::factory(), 
            'categorie_id' => Categorie::factory(),
        ];
    }
}