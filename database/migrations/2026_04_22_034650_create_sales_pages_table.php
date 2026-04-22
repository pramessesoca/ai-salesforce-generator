<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('product_name');
            $table->text('description');
            $table->json('key_features');
            $table->string('target_audience');
            $table->string('price');
            $table->json('unique_selling_points')->nullable();
            $table->string('headline');
            $table->string('subheadline');
            $table->text('product_description');
            $table->json('benefits');
            $table->json('features_breakdown');
            $table->text('social_proof_placeholder');
            $table->string('pricing_display');
            $table->string('cta_text');
            $table->string('cta_subtext');
            $table->json('full_payload');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_pages');
    }
};
