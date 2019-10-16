<?php

namespace App\Helper\View;

class InitialsHelper
{
    const MAX_LETTERS = 2;
    const PLACEHOLDER = '?';

    public static function getAuthorInitials(string $name): string
    {
        if (!$name) {
            return self::PLACEHOLDER;
        }

        $letters = '';
        $words = explode(' ', trim($name));
        $words = array_slice($words, 0, self::MAX_LETTERS);
        foreach ($words as $word) {
            $letters .= substr($word, 0, 1);
        }

        return $letters ?: self::PLACEHOLDER;
    }
}
