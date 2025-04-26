<?php

namespace PluginFrame\Core\Services\Options\Interfaces;

interface OptionInterface
{
    /**
     * Register a new option into WordPress options table.
     *
     * @param string $key
     * @param mixed $default
     * @param array $args Optional args like description, type, group, etc.
     * @return void
     */
    public function register(string $key, $default = null, array $args = []): void;

    /**
     * Get all registered options by this service.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Get a specific option value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Update a specific option value.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function update(string $key, $value): bool;

    /**
     * Delete a registered option.
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool;
}
