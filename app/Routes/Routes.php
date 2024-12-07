<?php

namespace PluginFrame\Routes;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

use PluginFrame\Routes\RoutesHandlers;
use PluginFrame\Routes\Middleware\AuthMiddleware;
use PluginFrame\Routes\Middleware\PublicMiddleware;
use PluginFrame\Routes\Middleware\RoleMiddleware;
use PluginFrame\Routes\Middleware\RateLimitMiddleware;
use PluginFrame\Routes\Middleware\CORSMiddleware;

class Routes extends RoutesHandlers
{
    private $currentMiddleware = [];

    public function registerRoutes()
    {
        // Register a route with an external class handler
        // $this->registerRoute('/data/add', [new AddNewPost(), 'addDataHandler'], 'POST', [
        //     AuthMiddleware::class,
        // ]);

        // Public Routes
        $this->group([PublicMiddleware::class], function ()
        {
            $this->registerRoute('/public-endpoint', 'publicEndpointHandler', 'GET');
            $this->registerRoute('/query-example', 'queryExampleHandler', 'GET');
        });

        // Authenticated Routes
        $this->group([AuthMiddleware::class], function ()
        {
            $this->registerRoute('/secure-data', 'getSecureData', 'GET');
            $this->registerRoute('/secure-data', 'postSecureData', 'POST');
            $this->registerRoute('/file-upload', 'fileUploadHandler', 'POST');
            $this->registerRoute('/custom-response', 'customResponseHandler', 'GET');
        });

        // Authentication Routes
        $this->registerRoute('/auth/application-password', 'handleApplicationPasswordAuth', 'POST', [PublicMiddleware::class]);
        $this->registerRoute('/auth/username-password', 'handleUsernamePasswordAuth', 'POST', [PublicMiddleware::class]);

        // Admin-Only Routes
        $this->group([AuthMiddleware::class, RoleMiddleware::class], function ()
        {
            $this->registerRoute('/admin-only', 'adminOnlyHandler', 'GET');
            $this->registerRoute('/nested-admin-data', 'nestedAdminDataHandler', 'GET');
        });

        // CRUD Operations
        $this->registerRoute('/data/add', 'addDataHandler', 'POST', [AuthMiddleware::class]);
        $this->registerRoute('/data/edit', 'editDataHandler', 'PUT', [AuthMiddleware::class]);
        $this->registerRoute('/data/delete', 'deleteDataHandler', 'DELETE', [AuthMiddleware::class]);
        $this->registerRoute('/data/get', 'getDataHandler', 'GET', [PublicMiddleware::class]);
        $this->registerRoute('/data/list', 'listDataHandler', 'GET', [AuthMiddleware::class]);
        $this->registerRoute('/data/restore', 'restoreDataHandler', 'POST', [AuthMiddleware::class]);
        $this->registerRoute('/data/bulk-delete', 'bulkDeleteHandler', 'POST', [AuthMiddleware::class]);
        $this->registerRoute('/data/bulk-update', 'bulkUpdateHandler', 'POST', [AuthMiddleware::class]);
        $this->registerRoute('/data/duplicate', 'duplicateDataHandler', 'POST', [AuthMiddleware::class]);

        // Export and Import Routes
        $this->registerRoute('/data/export', 'exportDataHandler', 'GET', [AuthMiddleware::class]);
        $this->registerRoute('/data/import', 'importDataHandler', 'POST', [AuthMiddleware::class]);
        
        // Example with multiple middleware: Auth and RateLimit
        $this->registerRoute('/rate-limited-endpoint', 'rateLimitedHandler', 'GET', [
            AuthMiddleware::class,
            RateLimitMiddleware::class,
        ]);

        // Example with CORS middleware
        $this->registerRoute('/cors-enabled-endpoint', 'corsHandler', 'GET', [
            CORSMiddleware::class,
        ]);

        // Example combining multiple middleware groups: Auth, Role, and CORS
        $this->registerRoute('/secure-data/admin', 'adminSecureDataHandler', 'GET', [
            AuthMiddleware::class,
            RoleMiddleware::class,
            CORSMiddleware::class,
        ]);

        // Example with deeply nested middleware
        $this->group([AuthMiddleware::class], function ()
        {
            $this->group([RoleMiddleware::class], function ()
            {
                $this->registerRoute('/nested-protected-endpoint', 'nestedProtectedHandler', 'GET', [
                    CORSMiddleware::class,
                ]);
            });
        });

        // Advanced Examples
        $this->registerRoute('/data/search', 'searchDataHandler', 'GET', [AuthMiddleware::class]);
        $this->registerRoute('/data/history', 'getDataHistoryHandler', 'GET', [AuthMiddleware::class]);
        $this->registerRoute('/error-example', 'errorExampleHandler', 'GET', [PublicMiddleware::class]);
    }

    /**
     * Register a single route
     */
    private function registerRoute($endpoint, $handlerMethod, $method = 'GET', $middleware = [])
    {
        $middlewareStack = $middleware ?: $this->currentMiddleware;

        register_rest_route('plugin-frame/v1', $endpoint, [
            'methods' => $method,
            'callback' => [$this, $handlerMethod],
            'permission_callback' => function ($request) use ($middlewareStack) {
                foreach ($middlewareStack as $middleware) {
                    $result = call_user_func([$middleware, 'handle'], $request);
                    if (is_wp_error($result)) {
                        return $result;
                    }
                }
                return true;
            },
        ]);
    }

    /**
     * Group routes with shared middleware
     */
    private function group(array $middleware, callable $callback)
    {
        $previousMiddleware = $this->currentMiddleware;
        $this->currentMiddleware = $middleware;

        $callback();

        $this->currentMiddleware = $previousMiddleware; // Restore previous middleware
    }

}
