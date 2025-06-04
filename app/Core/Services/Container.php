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
    public function get(string $id)
    {
        // 1) Return existing
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
    
        // 2) If you bound a factory, use it
        if (isset($this->definitions[$id])) {
            $service = $this->definitions[$id]($this);
            return $this->instances[$id] = $service;
        }
    
        // 3) Fallback to autowiring if the class exists
        if (class_exists($id)) {
            $service = $this->autowire($id);
            return $this->instances[$id] = $service;
        }
    
        // 4) Nothing found
        throw new class("Service {$id} not found")
            extends \Exception
            implements NotFoundExceptionInterface {};
    }
    
    /**
     * Autowire a class by reflecting its constructor arguments.
     *
     * @param string $class FQCN to instantiate
     * @return object
     */
    private function autowire(string $class)
    {
        $ref   = new \ReflectionClass($class);
        $ctor  = $ref->getConstructor();
        if (! $ctor || $ctor->getNumberOfParameters() === 0) {
            return new $class();
        }
    
        $args = [];
        foreach ($ctor->getParameters() as $param) {
            $type = $param->getType();
            if ($type && ! $type->isBuiltin()) {
                // recursively resolve from container
                $args[] = $this->get($type->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                // no type & no default: pass null or throw
                $args[] = null;
            }
        }
    
        return $ref->newInstanceArgs($args);
    }

    /**
     * Static shortcut to resolve a service.
     *
     * @param string $id
     * @return mixed
     */
    public static function resolve(string $id)
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
