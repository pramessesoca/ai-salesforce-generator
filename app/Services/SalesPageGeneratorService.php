<?php

namespace App\Services;

use App\Exceptions\SalesPageGenerationException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SalesPageGeneratorService
{
    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function generate(array $input): array
    {
        $apiKey = (string) config('services.gemini.api_key');
        $model = (string) config('services.gemini.model', 'gemini-3-flash-preview');

        if ($apiKey === '') {
            throw new SalesPageGenerationException('GEMINI_API_KEY is not configured.');
        }

        $prompt = $this->buildPrompt($input);

        try {
            $response = Http::retry(2, 500)
                ->timeout(30)
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/interactions',
                    [
                        'model' => $model,
                        'input' => $prompt,
                        'generation_config' => [
                            'temperature' => 0.8,
                        ],
                    ]
                );
        } catch (ConnectionException $e) {
            throw new SalesPageGenerationException(
                'Failed to reach AI service: '.$e->getMessage(),
                0,
                $e
            );
        }

        if (! $response->successful()) {
            throw new SalesPageGenerationException($this->buildApiErrorMessage($response));
        }

        $generatedText = $this->extractGeneratedText((array) $response->json());

        if (! is_string($generatedText) || trim($generatedText) === '') {
            throw new SalesPageGenerationException('AI response is empty.');
        }

        $decoded = $this->decodePayload($generatedText);

        return $this->normalizePayload($decoded, $input);
    }

    private function buildApiErrorMessage(Response $response): string
    {
        $status = $response->status();
        $apiMessage = data_get($response->json(), 'error.message');
        $apiStatus = data_get($response->json(), 'error.status');
        $apiCode = data_get($response->json(), 'error.code');
        $rawBody = trim($response->body());

        $details = [];

        if (is_string($apiMessage) && $apiMessage !== '') {
            $details[] = $apiMessage;
        }

        if ($apiStatus !== null) {
            $details[] = 'status='.$apiStatus;
        }

        if ($apiCode !== null) {
            $details[] = 'code='.$apiCode;
        }

        if ($details === [] && $rawBody !== '') {
            $details[] = mb_substr($rawBody, 0, 300);
        }

        $detailText = $details !== [] ? ' ('.implode(', ', $details).')' : '';

        return "AI service returned HTTP {$status}{$detailText}";
    }

    /**
     * @param array<string, mixed> $responseBody
     */
    private function extractGeneratedText(array $responseBody): string
    {
        $outputs = data_get($responseBody, 'outputs', []);

        if (is_array($outputs) && $outputs !== []) {
            $lastOutput = $outputs[array_key_last($outputs)];
            $text = data_get($lastOutput, 'text');

            if (is_string($text) && trim($text) !== '') {
                return $text;
            }
        }

        $fallbackText = data_get($responseBody, 'output_text');
        if (is_string($fallbackText) && trim($fallbackText) !== '') {
            return $fallbackText;
        }

        return '';
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    private function normalizePayload(array $payload, array $input): array
    {
        $benefits = array_values(array_filter((array) data_get($payload, 'benefits', []), 'is_string'));
        $featuresBreakdown = array_values(array_filter((array) data_get($payload, 'features_breakdown', []), 'is_string'));

        if ($benefits === [] || $featuresBreakdown === []) {
            throw new SalesPageGenerationException('AI output is incomplete.');
        }

        return [
            'headline' => (string) data_get($payload, 'headline', 'The best solution for your business'),
            'subheadline' => (string) data_get($payload, 'subheadline', 'Boost your results with a smarter approach.'),
            'product_description' => (string) data_get($payload, 'product_description', (string) ($input['description'] ?? '')),
            'benefits' => $benefits,
            'features_breakdown' => $featuresBreakdown,
            'social_proof_placeholder' => (string) data_get($payload, 'social_proof_placeholder', 'Place your best customer testimonials here.'),
            'pricing_display' => (string) data_get($payload, 'pricing_display', (string) ($input['price'] ?? '')),
            'cta_text' => (string) data_get($payload, 'cta_text', 'Get Started Now'),
            'cta_subtext' => (string) data_get($payload, 'cta_subtext', 'No commitment, cancel anytime.'),
            'full_payload' => $payload,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function decodePayload(string $generatedText): array
    {
        $raw = trim($generatedText);

        if (str_starts_with($raw, '```')) {
            $raw = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $raw) ?? $raw;
            $raw = trim($raw);
        }

        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            throw new SalesPageGenerationException('Failed to parse AI output format.');
        }

        return $decoded;
    }

    /**
     * @param array<string, mixed> $input
     */
    private function buildPrompt(array $input): string
    {
        $features = implode(', ', (array) ($input['key_features'] ?? []));
        $usps = implode(', ', (array) ($input['unique_selling_points'] ?? []));

        return <<<PROMPT
You are a direct-response copywriter for high-converting landing pages.

Use the following data:
- product_name: {$input['product_name']}
- description: {$input['description']}
- key_features: {$features}
- target_audience: {$input['target_audience']}
- price: {$input['price']}
- unique_selling_points: {$usps}

Task:
1) Write persuasive, clear, and natural sales copy in English.
2) Return ONLY valid JSON without markdown.
3) Use this exact schema:
{
  "headline": "string",
  "subheadline": "string",
  "product_description": "string",
  "benefits": ["string", "string", "string"],
  "features_breakdown": ["string", "string", "string"],
  "social_proof_placeholder": "string",
  "pricing_display": "string",
  "cta_text": "string",
  "cta_subtext": "string"
}

Make sure every field is filled and not empty.
PROMPT;
    }
}
