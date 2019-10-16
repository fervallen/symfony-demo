<?php

namespace App\Helper\View;

use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper as SymfonyTranslatorHelper;

class TranslateHelper
{
    /**
     * @var SymfonyTranslatorHelper
     */
    private static $symfonyTranslatorHelper;

    public static function text(string $textToTranslate, array $options = []): string
    {
        return self::$symfonyTranslatorHelper->trans($textToTranslate, $options);
    }

    public static function setSymfonyTranslatorHelper(SymfonyTranslatorHelper $symfonyTranslatorHelper): void
    {
        self::$symfonyTranslatorHelper = $symfonyTranslatorHelper;
    }
}
