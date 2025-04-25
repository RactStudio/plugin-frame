<?php
namespace PluginFrame\Core\Services\Options;

use Psr\Container\ContainerInterface;
use PluginFrame\Core\Services\Options\Interfaces\OptionStorageInterface;

/**
 * Central options service that delegates to one or more storage adapters.
 */
class OptionManager
{
    protected ContainerInterface $c;
    protected array $registered = [];

    public function __construct(ContainerInterface $container) {
        $this->c = $container;
    }

    public function register(string $key, $default = null, array $args = []): void {
        $drivers = $args['storage'] ?? ['wp'];
        $instances = array_map(fn($alias) =>
            $this->c->get(OptionStorageInterface::class . ':' . $alias),
            $drivers
        );
        $storage = count($instances) > 1
            ? new CompositeOptionStorage($instances)
            : $instances[0];

        $storage->register($key, $default, $args);
        $this->registered[$key] = ['default'=>$default,'args'=>$args];
    }

    public function get(string $key, $default = null) {
        // delegate to the selected storage(s)
        // ...
    }

    public function update(string $key, $value): bool {
        // delegate...
    }

    public function delete(string $key): bool {
        // delegate...
    }

    public function all(): array {
        return $this->registered;
    }
}
