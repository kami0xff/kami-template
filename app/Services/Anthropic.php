<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Minimal Anthropic Messages API client for the content commands
 * (site:write, site:cluster). Configure in config/services.php.
 */
class Anthropic
{
    /**
     * Send one user prompt and return the decoded JSON object from the
     * response text (models sometimes wrap JSON in prose — the outermost
     * {...} block is extracted).
     *
     * @throws \RuntimeException on missing key, API error, or non-JSON reply.
     */
    public function completeJson(string $prompt, int $maxTokens = 16384): array
    {
        $apiKey = config('services.anthropic.api_key');

        if (!$apiKey) {
            throw new \RuntimeException('ANTHROPIC_API_KEY is not set (config/services.php).');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(300)->post('https://api.anthropic.com/v1/messages', [
            'model' => config('services.anthropic.model', 'claude-sonnet-4-20250514'),
            'max_tokens' => $maxTokens,
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Anthropic API error: ' . $response->body());
        }

        $text = $response->json('content.0.text', '');

        if (preg_match('/\{[\s\S]*\}/u', $text, $m)) {
            $text = $m[0];
        }

        $data = json_decode($text, true);

        if (!is_array($data)) {
            throw new \RuntimeException('AI response was not valid JSON.');
        }

        return $data;
    }
}
