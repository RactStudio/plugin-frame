<?php

namespace PluginFrame\Cron;

class Helpers
{
    /**
     * Read a JSON file and decode it into an array.
     */
    public static function readJson(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);
        return json_decode($content, true) ?? [];
    }

    /**
     * Write an array to a JSON file.
     */
    public static function writeJson(string $filePath, array $data): void
    {
        $dir = dirname($filePath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
