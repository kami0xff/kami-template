<?php

/**
 * Locale Configuration
 *
 * Organized by region. Each locale: name, native name, flag emoji, RTL flag.
 * Trim this list to what your project actually needs.
 */

return [
    'default' => 'en',
    'fallback' => 'en',

    'supported' => [
        'en' => ['name' => 'English', 'native' => 'English', 'flag' => "\u{1F1EC}\u{1F1E7}", 'rtl' => false, 'region' => 'europe'],
        'es' => ['name' => 'Spanish', 'native' => 'Español', 'flag' => "\u{1F1EA}\u{1F1F8}", 'rtl' => false, 'region' => 'europe'],
        'fr' => ['name' => 'French', 'native' => 'Français', 'flag' => "\u{1F1EB}\u{1F1F7}", 'rtl' => false, 'region' => 'europe'],
        'de' => ['name' => 'German', 'native' => 'Deutsch', 'flag' => "\u{1F1E9}\u{1F1EA}", 'rtl' => false, 'region' => 'europe'],
        'it' => ['name' => 'Italian', 'native' => 'Italiano', 'flag' => "\u{1F1EE}\u{1F1F9}", 'rtl' => false, 'region' => 'europe'],
        'pt' => ['name' => 'Portuguese', 'native' => 'Português', 'flag' => "\u{1F1F5}\u{1F1F9}", 'rtl' => false, 'region' => 'europe'],
        'pt-BR' => ['name' => 'Brazilian Portuguese', 'native' => 'Português (Brasil)', 'flag' => "\u{1F1E7}\u{1F1F7}", 'rtl' => false, 'region' => 'americas'],
        'nl' => ['name' => 'Dutch', 'native' => 'Nederlands', 'flag' => "\u{1F1F3}\u{1F1F1}", 'rtl' => false, 'region' => 'europe'],
        'pl' => ['name' => 'Polish', 'native' => 'Polski', 'flag' => "\u{1F1F5}\u{1F1F1}", 'rtl' => false, 'region' => 'europe'],
        'ru' => ['name' => 'Russian', 'native' => 'Русский', 'flag' => "\u{1F1F7}\u{1F1FA}", 'rtl' => false, 'region' => 'europe'],
        'ja' => ['name' => 'Japanese', 'native' => '日本語', 'flag' => "\u{1F1EF}\u{1F1F5}", 'rtl' => false, 'region' => 'asia'],
        'ko' => ['name' => 'Korean', 'native' => '한국어', 'flag' => "\u{1F1F0}\u{1F1F7}", 'rtl' => false, 'region' => 'asia'],
        'zh' => ['name' => 'Chinese', 'native' => '简体中文', 'flag' => "\u{1F1E8}\u{1F1F3}", 'rtl' => false, 'region' => 'asia'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => "\u{1F1F8}\u{1F1E6}", 'rtl' => true, 'region' => 'middle_east'],
        'tr' => ['name' => 'Turkish', 'native' => 'Türkçe', 'flag' => "\u{1F1F9}\u{1F1F7}", 'rtl' => false, 'region' => 'europe'],
    ],

    'priority' => [
        'en', 'es', 'fr', 'de', 'pt', 'it', 'nl', 'pl', 'ru',
        'ja', 'ko', 'zh', 'ar', 'tr', 'pt-BR',
    ],

    'rtl' => ['ar', 'he', 'fa', 'ur'],

    'groups' => [
        'europe_west' => ['en', 'es', 'fr', 'de', 'it', 'pt', 'nl'],
        'europe_east' => ['pl', 'ru'],
        'east_asia' => ['zh', 'ja', 'ko'],
        'middle_east' => ['ar', 'tr'],
        'americas' => ['pt-BR'],
    ],
];
