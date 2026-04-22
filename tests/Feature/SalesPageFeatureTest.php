<?php

namespace Tests\Feature;

use App\Models\SalesPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SalesPageFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_protected_routes(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_generate_and_store_sales_page(): void
    {
        config()->set('services.gemini.api_key', 'fake-key');
        config()->set('services.gemini.model', 'gemini-2.5-flash');

        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'headline' => 'Naikkan Penjualan 2x Lebih Cepat',
                                        'subheadline' => 'Sistem praktis untuk tim kecil hingga besar',
                                        'product_description' => 'Deskripsi AI',
                                        'benefits' => ['Benefit 1', 'Benefit 2', 'Benefit 3'],
                                        'features_breakdown' => ['Fitur 1', 'Fitur 2', 'Fitur 3'],
                                        'social_proof_placeholder' => 'Dipercaya 1.000+ pelanggan',
                                        'pricing_display' => 'Rp 199.000/bulan',
                                        'cta_text' => 'Coba Sekarang',
                                        'cta_subtext' => 'Tanpa kartu kredit',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('sales-pages.generate'), [
            'product_name' => 'CRM Kilat',
            'description' => 'CRM untuk UMKM',
            'key_features' => ['Pipeline visual', 'WhatsApp reminder'],
            'target_audience' => 'Pemilik UMKM',
            'price' => 'Rp 199.000/bulan',
            'unique_selling_points' => ['Onboarding 1 hari', 'Tanpa setup ribet'],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('sales_pages', [
            'user_id' => $user->id,
            'product_name' => 'CRM Kilat',
            'headline' => 'Naikkan Penjualan 2x Lebih Cepat',
        ]);
    }

    public function test_generation_validation_fails_for_invalid_payload(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('sales-pages.generate'), [
            'product_name' => '',
            'description' => '',
            'key_features' => [],
            'target_audience' => '',
            'price' => '',
            'unique_selling_points' => [],
        ]);

        $response->assertSessionHasErrors(['product_name', 'description', 'key_features', 'target_audience', 'price']);
    }

    public function test_generation_handles_invalid_ai_response_gracefully(): void
    {
        config()->set('services.gemini.api_key', 'fake-key');
        config()->set('services.gemini.model', 'gemini-2.5-flash');

        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [['text' => 'invalid json']],
                        ],
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('dashboard'))->post(route('sales-pages.generate'), [
            'product_name' => 'CRM Kilat',
            'description' => 'CRM untuk UMKM',
            'key_features' => ['Pipeline visual'],
            'target_audience' => 'Pemilik UMKM',
            'price' => 'Rp 199.000/bulan',
            'unique_selling_points' => ['Onboarding 1 hari'],
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasErrors(['generation']);
    }

    public function test_user_cannot_access_another_users_sales_page(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $salesPage = SalesPage::factory()->for($owner)->create();

        $this->actingAs($other)
            ->get(route('sales-pages.show', $salesPage))
            ->assertForbidden();

        $this->actingAs($other)
            ->delete(route('sales-pages.destroy', $salesPage))
            ->assertForbidden();
    }

    public function test_regenerate_updates_generated_fields_without_overwriting_input(): void
    {
        config()->set('services.gemini.api_key', 'fake-key');
        config()->set('services.gemini.model', 'gemini-2.5-flash');

        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'headline' => 'Headline Baru',
                                        'subheadline' => 'Subheadline Baru',
                                        'product_description' => 'Deskripsi Baru',
                                        'benefits' => ['Benefit A', 'Benefit B', 'Benefit C'],
                                        'features_breakdown' => ['Feature A', 'Feature B', 'Feature C'],
                                        'social_proof_placeholder' => 'Social proof baru',
                                        'pricing_display' => 'Rp 299.000/bulan',
                                        'cta_text' => 'Mulai Hari Ini',
                                        'cta_subtext' => 'Promo terbatas',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $salesPage = SalesPage::factory()->for($user)->create([
            'product_name' => 'Produk Lama',
            'headline' => 'Headline Lama',
        ]);

        $this->actingAs($user)
            ->post(route('sales-pages.regenerate', $salesPage))
            ->assertRedirect();

        $salesPage->refresh();

        $this->assertSame('Produk Lama', $salesPage->product_name);
        $this->assertSame('Headline Baru', $salesPage->headline);
    }
}
