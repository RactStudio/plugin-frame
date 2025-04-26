<?php

namespace PluginFrame\Core\Services\Options;

use PluginFrame\Core\Services\Options\Interfaces\OptionStorageInterface;

/**
 * Stores options in WordPress's wp_options table via the Options API.
 */
class WPOptionStorage implements OptionStorageInterface
{
    public function register(string $key, $default = null, array $args = []): void
    {
        if (false === get_option($key)) {
            add_option($key, $default);
        }
    }

    public function get(string $key, $default = null)
    {
        return get_option($key, $default);
    }

    public function update(string $key, $value): bool
    {
        return update_option($key, $value);
    }

    public function delete(string $key): bool
    {
        return delete_option($key);
    }

    public function all(): array
    {
        // Note: WP doesn't offer get_options() until 6.4; fallback to iterating your registry.
        return [];
    }
}
