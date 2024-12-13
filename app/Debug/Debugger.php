<?php

namespace PluginFrame\Debug;

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
        echo self::render_html($data);
    }

    private static function render_html($data): string
    {
        $html = "<div style='background: #333; color: #fff; padding: 10px; border-radius: 4px; max-height: 400px; overflow: auto;'>";
        $html .= "<button onclick='toggleAll()' style='margin-bottom: 10px; padding: 5px 10px; background: #0073aa; color: #fff; border: none; border-radius: 3px; cursor: pointer;'>Expand/Collapse All</button>";
        $html .= "<div>";
        $html .= self::array_to_html($data);
        $html .= "</div>";
        $html .= "</div>";

        $html .= "<script>
        function toggleAll() {
            const details = document.querySelectorAll('details');
            const expand = !Array.from(details).every(detail => detail.open); // Check if all are expanded
            details.forEach(detail => (detail.open = expand)); // Set all to the same state
        }
        </script>";

        return $html;
    }

    private static function array_to_html($array, $level = 0): string
    {
        if (!is_array($array)) {
            return htmlspecialchars(self::value_to_string($array));
        }

        $html = "<ul style='list-style-type: none; padding-left: 10px;'>";

        foreach ($array as $key => $value) {
            $keyLabel = is_int($key) ? "Item {$key}" : htmlspecialchars((string)$key);

            if (is_array($value)) {
                $html .= "<li>";
                $html .= "<details>";
                $html .= "<summary><b>{$keyLabel}</b></summary>";
                $html .= self::array_to_html($value, $level + 1);
                $html .= "</details>";
                $html .= "</li>";
            } elseif (is_object($value)) {
                $html .= "<li><b>{$keyLabel}:</b> <a href='#' style='color: #1e90ff; text-decoration: underline;' onclick='alert(JSON.stringify(" . json_encode($value) . "))'>" . htmlspecialchars(self::value_to_string($value)) . "</a></li>";
            } else {
                $html .= "<li><b>{$keyLabel}:</b> <a href='#' style='color: #1e90ff; text-decoration: underline;' onclick='alert(" . json_encode($value) . ")'>" . htmlspecialchars((string)$value) . "</a></li>";
            }
        }

        $html .= "</ul>";
        return $html;
    }

    private static function value_to_string($value): string
    {
        if (is_null($value)) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string)$value;
            }

            return json_encode($value) ?: 'Instance of ' . get_class($value);
        }

        return (string)$value;
    }
}
