<?php
namespace PluginFrame\Core\Services\Options;

use PluginFrame\Core\Services\Options\Interfaces\OptionStorageInterface;
use wpdb;

/**
 * Stores options in a custom DB table for high-volume or structured data.
 */
class CustomTableOption implements OptionStorageInterface
{
    private wpdb $db;
    private string $table;

    public function __construct()
    {
        global $wpdb;
        $this->db    = $wpdb;
        $this->table = $wpdb->prefix . 'pf_options';
    }

    public function register(string $key, $default = null, array $args = []): void
    {
        $exists = $this->db->get_var(
            $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE option_key=%s", $key)
        );
        if (!$exists) {
            $this->db->insert(
                $this->table,
                ['option_key' => $key, 'option_value' => maybe_serialize($default)],
                ['%s','%s']
            );
        }
    }

    public function get(string $key, $default = null)
    {
        $row = $this->db->get_row(
            $this->db->prepare("SELECT option_value FROM {$this->table} WHERE option_key=%s", $key),
            ARRAY_A
        );
        return $row ? maybe_unserialize($row['option_value']) : $default;
    }

    public function update(string $key, $value): bool
    {
        return (bool) $this->db->update(
            $this->table,
            ['option_value' => maybe_serialize($value)],
            ['option_key'   => $key],
            ['%s'],
            ['%s']
        );
    }

    public function delete(string $key): bool
    {
        return (bool) $this->db->delete($this->table, ['option_key' => $key], ['%s']);
    }

    public function all(): array
    {
        $rows = $this->db->get_results("SELECT * FROM {$this->table}", ARRAY_A);
        return array_column($rows, 'option_value', 'option_key');
    }
}
