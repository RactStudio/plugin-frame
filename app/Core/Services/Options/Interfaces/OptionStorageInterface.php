<?php
namespace PluginFrame\Core\Services\Options\Interfaces;

/**
 * Defines a storage strategy for plugin frame options.
 */
interface OptionStorageInterface
{
    public function register(string $key, $default = null, array $args = []): void;
    public function get(string $key, $default = null);
    public function update(string $key, $value): bool;
    public function delete(string $key): bool;
    public function all(): array;
}
