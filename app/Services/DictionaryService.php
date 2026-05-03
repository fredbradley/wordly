<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DictionaryService
{
    private const API = 'https://api.dictionaryapi.dev/api/v2/entries/en/';

    public function isRealWord(string $word): bool
    {
        try {
            $response = Http::timeout(8)->get(self::API . urlencode(strtolower($word)));

            // 200 + JSON array = found; 404 + JSON object = not found
            return $response->successful() && is_array($response->json());
        } catch (\Throwable $e) {
            // Network error — fail open so we don't wrongly reject words
            Log::warning("DictionaryService: could not reach API for '{$word}': {$e->getMessage()}");
            return true;
        }
    }
}
