<?php

namespace app\services;

class KeywordNormalizer
{
    public static function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = str_replace('ё', 'е', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return $text;
    }

    public static function detectLanguage(string $text): string
    {
        $hasCyrillic = (bool) preg_match('/[\p{Cyrillic}]/u', $text);
        $hasLatin = (bool) preg_match('/[a-z]/i', $text);

        if ($hasCyrillic && $hasLatin) {
            return 'mixed';
        }
        if ($hasCyrillic) {
            return 'ru';
        }
        if ($hasLatin) {
            return 'en';
        }
        return 'mixed';
    }
}
