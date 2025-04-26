<?php
namespace PluginFrame\Core\Services;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * A simple PSR-11–compatible service container.
 * Implements a static singleton for global access.
 */
class Container implements ContainerInterface
{
    /** @var Container|null */
    private static ?Container $instance = null;

    /** @var array<string, callable> */
    private array $definitions = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    /** Private to enforce singleton */
    private function __construct() {}

    /**
     * Retrieve the singleton instance.
     */
    public static function getInstance(): Container
    {
        return self::$instance ??= new Container();
    }

    /**
     * Bind a service ID to a resolver callable.
     *
     * @param string   $id       Service identifier.
     * @param callable $resolver function(ContainerInterface): mixed
     */
    public static function bind(string $id, callable $resolver): void
    {
        self::getInstance()->definitions[$id] = $resolver;
    }

    /**
     * Resolve a service by its ID.
     *
     * @param string $id Identifier of the entry to look for.
     * @return mixed    The entry.
     * @throws NotFoundExceptionInterface No entry was found for this identifier.
     */
    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (! isset($this->definitions[$id])) {
            throw new class("Service {$id} not found")
                extends \Exception
                implements NotFoundExceptionInterface {};
        }

        $resolver          = $this->definitions[$id];
        $service           = $resolver($this);
        $this->instances[$id] = $service;
        return $service;
    }

    /**
     * Static shortcut to resolve a service.
     *
     * @param string $id
     * @return mixed
     */
    public static function resolve(string $id): mixed
    {
        return self::getInstance()->get($id);
    }

    /**
     * Does this container have a resolver bound for $id?
     *
     * @param string $id Identifier to check.
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }
}
