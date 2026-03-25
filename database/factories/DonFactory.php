<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;     
use App\Models\Campagne;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Don>
 */
class DonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'montant' => fake()->randomFloat(2, 10, 500),
            
            
            'donateur_id' => User::where('role', 'donateur')->inRandomOrder()->first()?->id ?? User::factory(),
            
            
            'campagne_id' => Campagne::inRandomOrder()->first()?->id ?? Campagne::factory(),
        ];
    }
}
