<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'description',
        'key_features',
        'target_audience',
        'price',
        'unique_selling_points',
        'headline',
        'subheadline',
        'product_description',
        'benefits',
        'features_breakdown',
        'social_proof_placeholder',
        'pricing_display',
        'cta_text',
        'cta_subtext',
        'full_payload',
    ];

    protected function casts(): array
    {
        return [
            'key_features' => 'array',
            'unique_selling_points' => 'array',
            'benefits' => 'array',
            'features_breakdown' => 'array',
            'full_payload' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
