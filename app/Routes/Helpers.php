<?php

namespace PluginFrame\Routes;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Helpers
{
    private $currentMiddleware = [];
    private $routesBase;
    private $middlewareCache = [];
    private $prefixStack = [];
    private $routeSpecificMiddleware = [];

    /**
     * Constructor to set a dynamic route prefix.
     *
     * @param string $routesBase The base URL prefix for the routes (default: 'plugin-frame/v1').
     */
    public function __construct($routesBase = 'plugin-frame/v1')
    {
        $this->routesBase = $routesBase;
    }

    /**
     * Register a single REST API route.
     *
     * @param string $method HTTP method (GET, POST, etc.).
     * @param string $endpoint The route endpoint.
     * @param callable|string|array $handler A callable, a class, or an array with class and method.
     * @param array $middleware Middleware classes to execute before the handler.
     */
    public function single($method, $endpoint, $handler, $middleware = []): void
    {
        // Apply all stacked prefixes
        $fullEndpoint = implode('/', $this->prefixStack) . '/' . ltrim($endpoint, '/');
    
        $middlewareStack = $middleware ?: $this->routeSpecificMiddleware ?: $this->currentMiddleware;
    
        register_rest_route($this->routesBase, $fullEndpoint, [
            'methods' => strtoupper($method),
            'callback' => function ($request) use ($handler, $middlewareStack) {
                $middlewareResult = $this->executeMiddleware($request, $middlewareStack);
                if (is_wp_error($middlewareResult)) {
                    return $middlewareResult;
                }
                return $this->handleMethodHandler($handler, $request);
            },
            'permission_callback' => function ($request) use ($middlewareStack) {
                if (empty($middlewareStack)) {
                    return true;
                }
                $middlewareResult = $this->executeMiddleware($request, $middlewareStack, true);
                return !is_wp_error($middlewareResult);
            },
        ]);
    }

    /**
     * Add a prefix to the multiple route group / single routes.
     *
     * @param string $prefix The URL prefix for the group.
     * @param callable $callback A callback that registers routes within this group.
     */
    public function prefix($prefix, callable $callback): void
    {
        // Add the current prefix to the stack
        array_push($this->prefixStack, trim($prefix, '/'));

        // Execute the callback within this prefix context
        $callback();

        // Remove the last added prefix
        array_pop($this->prefixStack);
    }

    /**
     * Group multiple routes with shared middleware.
     *
     * @param callable $callback A callback that registers the routes in the group.
     * @param array $middleware Optional middleware classes to apply to the group.
     */
    public function group(callable $callback, array $middleware = []): void
    {
        $previousMiddleware = $this->currentMiddleware;
        $this->currentMiddleware = array_merge($previousMiddleware, $middleware);
    
        $callback();
    
        $this->currentMiddleware = $previousMiddleware; // Restore middleware
    }    

    /**
     * Temporarily remove middleware for a group of routes.
     *
     * @param callable $callback The callback that registers routes in the context.
     * @param string|array|null $middleware The specific middleware(s) to remove, or null to remove all.
     */
    public function removeMiddleware(callable $callback, $middleware = null): void
    {
        $originalMiddleware = $this->currentMiddleware;

        if ($middleware) {
            // Remove specific middleware
            $middlewareToRemove = is_array($middleware) ? $middleware : [$middleware];
            $this->currentMiddleware = array_diff($originalMiddleware, $middlewareToRemove);
        } else {
            // Remove all middleware
            $this->currentMiddleware = [];
        }

        // Execute the callback with the modified middleware stack
        $callback();

        // Restore the original middleware stack
        $this->currentMiddleware = $originalMiddleware;
    }

    /**
     * Execute middleware stack for a request.
     *
     * @param \WP_REST_Request $request The current request object.
     * @param array $middlewareStack List of middleware classes.
     * @param bool $isPermissionCheck Whether this is for a permission check.
     *
     * @return true|\WP_Error True if middleware passes, or WP_Error if it fails.
     */
    private function executeMiddleware($request, array $middlewareStack, $isPermissionCheck = false)
    {
        if (empty($middlewareStack)) {
            return true; // No middleware, allow access
        }

        $cacheKey = md5(json_encode($middlewareStack) . ($isPermissionCheck ? '_permission' : '_callback'));

        // Return cached result if available
        if (isset($this->middlewareCache[$cacheKey])) {
            return $this->middlewareCache[$cacheKey];
        }

        foreach ($middlewareStack as $middleware) {
            if (!class_exists($middleware)) {
                $this->logError("Invalid middleware: $middleware.");
                return new \WP_Error('invalid_middleware', "Middleware class '$middleware' is invalid.", ['status' => 500]);
            }

            if (method_exists($middleware, 'handle')) {
                $reflection = new \ReflectionMethod($middleware, 'handle');
                if ($reflection->isStatic()) {
                    $result = call_user_func([$middleware, 'handle'], $request);
                } else {
                    $instance = new $middleware();
                    $result = $instance->handle($request);
                }

                if (is_wp_error($result)) {
                    $this->middlewareCache[$cacheKey] = $result; // Cache failure
                    return $result; // Stop execution if middleware fails
                }
            } else {
                $this->logError("Middleware class '$middleware' is missing a 'handle' method.");
                return new \WP_Error('invalid_middleware', "Middleware class '$middleware' is missing a 'handle' method.", ['status' => 500]);
            }
        }

        $this->middlewareCache[$cacheKey] = true; // Cache success
        return true;
    }

    /**
     * Invoke a handler dynamically.
     *
     * @param callable|string|array $handler The handler to invoke.
     * @param \WP_REST_Request $request The request object.
     *
     * @return mixed The handler result or a WP_Error.
     */
    private function handleMethodHandler($handler, $request)
    {
        if (is_callable($handler)) {
            return call_user_func($handler, $request);
        }

        if (is_string($handler) && class_exists($handler)) {
            $instance = new $handler();

            if (method_exists($instance, 'handle')) {
                return $instance->handle($request);
            }
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;

            if (class_exists($class)) {
                $instance = new $class();

                if (method_exists($instance, $method)) {
                    return call_user_func([$instance, $method], $request);
                }
            }
        }

        $this->logError("Invalid handler provided.");
        return new \WP_Error('invalid_handler', 'The handler provided is invalid.', ['status' => 500]);
    }

    /**
     * Log an error message.
     *
     * @param string $message The error message to log.
     */
    private function logError($message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("[PluginFrame Error] $message");
        }
    }
}
