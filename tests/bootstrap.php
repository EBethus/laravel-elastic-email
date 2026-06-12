<?php

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('App')) {
    class App
    {
        protected static string $locale = 'en';

        public static function getLocale(): string
        {
            return static::$locale;
        }

        public static function setLocale(string $locale): void
        {
            static::$locale = $locale;
        }
    }
}
