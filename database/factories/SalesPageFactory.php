<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesPage>
 */
class SalesPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'key_features' => [fake()->word(), fake()->word()],
            'target_audience' => fake()->jobTitle(),
            'price' => 'Rp '.fake()->numberBetween(100000, 1000000),
            'unique_selling_points' => [fake()->sentence(3), fake()->sentence(3)],
            'headline' => fake()->sentence(),
            'subheadline' => fake()->sentence(),
            'product_description' => fake()->paragraph(),
            'benefits' => [fake()->sentence(), fake()->sentence(), fake()->sentence()],
            'features_breakdown' => [fake()->sentence(), fake()->sentence(), fake()->sentence()],
            'social_proof_placeholder' => fake()->sentence(),
            'pricing_display' => 'Mulai dari Rp 99.000',
            'cta_text' => 'Daftar Sekarang',
            'cta_subtext' => 'Uji coba gratis 7 hari',
            'full_payload' => [
                'source' => 'factory',
            ],
        ];
    }
}
