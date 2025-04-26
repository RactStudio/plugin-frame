<?php
namespace PluginFrame\Core\Services\Options;

use PluginFrame\Core\Services\Options\Interfaces\OptionStorageInterface;

/**
 * Composite driver: writes to multiple storage backends simultaneously.
 */
class CompositeOptionStorage implements OptionStorageInterface
{
    /** @var OptionStorageInterface[] */
    private array $storages;

    public function __construct(array $storages) {
        $this->storages = $storages;
    }

    public function register(string $key, $default = null, array $args = []): void {
        foreach ($this->storages as $storage) {
            $storage->register($key, $default, $args);
        }
    }

    public function get(string $key, $default = null) {
        foreach ($this->storages as $storage) {
            $value = $storage->get($key, null);
            if (null !== $value) {
                return $value;
            }
        }
        return $default;
    }

    public function update(string $key, $value): bool {
        $ok = true;
        foreach ($this->storages as $storage) {
            $ok = $ok && $storage->update($key, $value);
        }
        return $ok;
    }

    public function delete(string $key): bool {
        $ok = true;
        foreach ($this->storages as $storage) {
            $ok = $ok && $storage->delete($key);
        }
        return $ok;
    }

    public function all(): array {
        $all = [];
        foreach ($this->storages as $storage) {
            $all = array_merge($all, $storage->all());
        }
        return $all;
    }
}
