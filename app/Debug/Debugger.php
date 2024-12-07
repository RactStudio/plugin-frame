<?php

namespace PluginFrame\Debug;

use Knit\Knit;

/**
 * Debugger Class
 */
class Debugger
{
    public static function dd($data): void
    {
        self::render($data);
        exit;
    }

    public static function d($data): void
    {
        self::render($data);
    }

    public static function log($data): void
    {
        error_log(print_r($data, true));
    }

    public static function json($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public static function backtrace(): void
    {
        $backtrace = debug_backtrace();
        self::render($backtrace);
    }

    public static function render($data): void
    {
        // Use Knit Debug to handle visualization
        if (is_array($data) || is_object($data)) {
            echo self::render_html($data);
        } else {
            Knit::debug($data);
        }
    }

    private static function render_html($data): string
    {
        $output = "<pre style='background: #333; color: #fff; padding: 10px; border-radius: 4px; max-height: 400px; overflow: auto;'>";

        if (is_array($data)) {
            $output .= self::array_to_html($data);
        } else {
            $output .= htmlspecialchars(print_r($data, true));
        }

        $output .= "</pre>";
        return $output;
    }

    private static function array_to_html($array, $level = 0): string
    {
        $html = "<ul style='list-style-type: none; padding-left: 20px;'>";

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $html .= "<li>";
                $html .= "<details>";
                $html .= "<summary><b>{$key}</b></summary>";
                $html .= self::array_to_html($value, $level + 1);
                $html .= "</details>";
                $html .= "</li>";
            } else {
                $html .= "<li><b>{$key}:</b> " . htmlspecialchars($value) . "</li>";
            }
        }

        $html .= "</ul>";
        return $html;
    }
}
