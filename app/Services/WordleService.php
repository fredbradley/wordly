<?php

namespace App\Services;

class WordleService
{
    public function evaluate(string $guess, string $target): array
    {
        $guess  = strtolower($guess);
        $target = strtolower($target);
        $result = array_fill(0, 5, 'absent');

        // First pass: mark correct positions
        $targetLetters = str_split($target);
        for ($i = 0; $i < 5; $i++) {
            if ($guess[$i] === $target[$i]) {
                $result[$i] = 'correct';
                $targetLetters[$i] = null;
            }
        }

        // Second pass: mark present (wrong position)
        for ($i = 0; $i < 5; $i++) {
            if ($result[$i] === 'correct') continue;
            $pos = array_search($guess[$i], $targetLetters);
            if ($pos !== false) {
                $result[$i] = 'present';
                $targetLetters[$pos] = null;
            }
        }

        return $result;
    }

    public function isValidWord(string $word): bool
    {
        return strlen($word) === 5 && ctype_alpha($word);
    }
}
