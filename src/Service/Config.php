<?php

namespace TimeLogger\Service;

class Config
{
    public function getBaseUrl(): string
    {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === 0 ? 'https://' : 'http://';

        return sprintf("%s%s", $protocol, $_SERVER['SERVER_NAME'] . '/');
    }

    public static function getDataFilePath(): string
    {
        return dirname(__DIR__, 2) . '/var/data/data.json';
    }

    public static function isProductionEnvironment(): bool
    {
        return isset($_ENV['APPLICATION_MODE']) &&
            str_contains($_ENV['APPLICATION_MODE'], 'prod');
    }
}
