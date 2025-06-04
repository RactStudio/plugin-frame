<?php

namespace PluginFrame\Core\Services\Options;

use Psr\Container\ContainerInterface;
use PluginFrame\Core\Services\Options\Interfaces\OptionStorageInterface;
use PluginFrame\Core\Services\Options\CompositeOptionStorage;
use InvalidArgumentException;

/**
 * Central options service that delegates to one or more storage adapters.
 *
 * Stores metadata about registered options (default value, storage drivers,
 * customizer flags) and proxies all reads/writes/deletes to the configured
 * storage backends.
 */
class OptionManager
{
    /** @var ContainerInterface */
    protected ContainerInterface $c;

    /**
     * Metadata for all registered options.
     * ```
     * [
     *   'option_key' => [
     *     'default' => mixed,
     *     'args'    => [
     *       'storage'    => array<string>, // ['wp'], ['custom'], or ['wp','custom']
     *       'group'      => string|null,
     *       'customizer' => bool|array,
     *       // ...other metadata
     *     ]
     *   ],
     *   // ...
     * ]
     * ```
     * @var array<string, array{default: mixed, args: array}>
     */
    protected array $registered = [];

    /**
     * @param ContainerInterface $container PSR-11 container with storage services bound
     */
    public function __construct(ContainerInterface $container)
    {
        $this->c = $container;
    }

    /**
     * Register a new option under one or more storage backends.
     *
     * @param string       $key     Unique option name.
     * @param mixed|null   $default Default value if none exists.
     * @param array<string,mixed> $args
     * ```
     *     [
     *       'storage'    => array<string>, // storage aliases
     *       'group'      => string|null,
     *       'customizer' => bool|array,
     *       // ...any other metadata
     *     ]
     * ```
     * @return void
     */
    public function register(string $key, $default = null, array $args = []): void
    {
        $drivers = $args['storage'] ?? ['wp'];
        $instances = array_map(
            fn(string $alias) => $this->c->get(OptionStorageInterface::class . ':' . $alias),
            $drivers
        );

        $storage = count($instances) > 1
            ? new CompositeOptionStorage($instances)
            : $instances[0];

        $storage->register($key, $default, $args);

        $this->registered[$key] = [
            'default' => $default,
            'args'    => $args,
        ];
    }

    /**
     * Retrieve an option value from its configured storage.
     *
     * @param string     $key     Option name.
     * @param mixed|null $default Default if not found.
     * @return mixed
     * @throws InvalidArgumentException if the option was not registered.
     */
    public function get(string $key, $default = null)
    {
        if (! isset($this->registered[$key])) {
            throw new InvalidArgumentException("Option '{$key}' has not been registered.");
        }

        $drivers = $this->registered[$key]['args']['storage'] ?? ['wp'];
        $instances = array_map(
            fn(string $alias) => $this->c->get(OptionStorageInterface::class . ':' . $alias),
            $drivers
        );

        $storage = count($instances) > 1
            ? new CompositeOptionStorage($instances)
            : $instances[0];

        return $storage->get($key, $default);
    }

    /**
     * Update an option value in its configured storage(s).
     *
     * @param string $key   Option name.
     * @param mixed  $value New value to store.
     * @return bool         True if all updates succeeded; false otherwise.
     * @throws InvalidArgumentException if the option was not registered.
     */
    public function update(string $key, $value): bool
    {
        if (! isset($this->registered[$key])) {
            throw new InvalidArgumentException("Option '{$key}' has not been registered.");
        }

        $drivers = $this->registered[$key]['args']['storage'] ?? ['wp'];
        $instances = array_map(
            fn(string $alias) => $this->c->get(OptionStorageInterface::class . ':' . $alias),
            $drivers
        );

        $storage = count($instances) > 1
            ? new CompositeOptionStorage($instances)
            : $instances[0];

        return $storage->update($key, $value);
    }

    /**
     * Delete an option from its configured storage(s) and the registry.
     *
     * @param string $key Option name.
     * @return bool       True if all deletes succeeded; false otherwise.
     * @throws InvalidArgumentException if the option was not registered.
     */
    public function delete(string $key): bool
    {
        if (! isset($this->registered[$key])) {
            throw new InvalidArgumentException("Option '{$key}' has not been registered.");
        }

        $drivers = $this->registered[$key]['args']['storage'] ?? ['wp'];
        $instances = array_map(
            fn(string $alias) => $this->c->get(OptionStorageInterface::class . ':' . $alias),
            $drivers
        );

        $storage = count($instances) > 1
            ? new CompositeOptionStorage($instances)
            : $instances[0];

        $success = $storage->delete($key);
        if ($success) {
            unset($this->registered[$key]);
        }

        return $success;
    }

    /**
     * Get metadata for all registered options.
     *
     * @return array<string, array{default: mixed, args: array}>
     */
    public function all(): array
    {
        return $this->registered;
    }
}
