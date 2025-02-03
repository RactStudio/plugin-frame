<?php

namespace PluginFrame\Config;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

use PluginFrame\Routes\Helpers;

// Middleware classes
use PluginFrame\Routes\Middleware\PublicMiddleware;
use PluginFrame\Routes\Middleware\CORSMiddleware;
use PluginFrame\Routes\Middleware\AuthMiddleware;
use PluginFrame\Routes\Middleware\AuthUserPassMiddleware;
use PluginFrame\Routes\Middleware\AuthAppPassMiddleware;
use PluginFrame\Routes\Middleware\RoleMiddleware;
use PluginFrame\Routes\Middleware\RateLimitMiddleware;

// Handlers classes
use PluginFrame\Routes\Handlers\TestData;
use PluginFrame\Routes\Handlers\HandleData;
use PluginFrame\Routes\Handlers\PublicData;
use PluginFrame\Routes\Handlers\DemoData;
use PluginFrame\Routes\Handlers\OtherHandlers;

class Routes
{
    public function __construct()
    {
        // Can be assigned for multiple route base url by creating a new instance
        $route = new Helpers('plugin-frame/v1');

        // Public Endpoint Routes with No middleware
        $route->single('get', '/db-posts', [new TestData(), 'testDBposts']);

        // Public Endpoint Routes with No middleware
        $route->single('get', '/nowar', [new TestData(), 'testDataHandler']);

        // Public Endpoint Routes with new class and method handler
        $route->single('get', '/test', [new TestData(), 'testDataHandler'], [PublicMiddleware::class, RateLimitMiddleware::class]);

        // Signin Endpoint Routes with new class and method handler
        $route->single('get', '/signin', [new TestData(), 'testDataHandler'], [AuthUserPassMiddleware::class, RateLimitMiddleware::class]);

        $route->group(
            function () use($route)
            {
                $route->single('get', '/public', [new PublicData(), 'publicDataHandler']);
            },
            [PublicMiddleware::class, RateLimitMiddleware::class]
        );

        // Single route with class and static method
        $route->single('get', '/test-1', [TestData::class, 'testDataHandler'], [
            PublicMiddleware::class,
            RateLimitMiddleware::class,
        ]);

        // Grouped routes with middleware and static method handler
        $route->group(
            function () use ($route)
            {
                $route->single('get', '/public-1', [PublicData::class, 'publicDataHandler']);
            },
            [PublicMiddleware::class, RateLimitMiddleware::class]
        );

        // Single route using a class's handle method dynamically by autoloading handle method
        $route->single('get', '/test-data', HandleData::class, [
            PublicMiddleware::class,
            RateLimitMiddleware::class,
        ]);

        // Grouped route using a class's handle method by automatically calling handle method
        $route->group(
            function () use ($route)
            {
                $route->single('get', '/demo-data', DemoData::class);

                // Remove only RateLimitMiddleware for specific routes (support for multiple middleware in an array)
                $route->removeMiddleware(
                    function () use ($route)
                    {
                        $route->single('get', '/without-rate-limit', [TestData::class, 'testDataHandler']);
                        $route->single('post', '/without-rate-limit', [TestData::class, 'testDataHandler']);
                    },
                    RateLimitMiddleware::class
                );

                // Remove all middleware for specific routes
                $route->removeMiddleware(function () use ($route)
                    {
                        $route->single('get', '/without-any-middleware', [TestData::class, 'testDataHandler']);
                        $route->single('post', '/without-any-middleware', [TestData::class, 'testDataHandler']);
                    }
                );

                $route->single('post', '/demo-data', DemoData::class);
            },
            [PublicMiddleware::class, RateLimitMiddleware::class]
        );

        // Prefix with multiple single routes
        $route->prefix('/example-one', function () use ($route)
        {
            // Single route under '/example-one/route-a'
            $route->single('get', '/route-a', [TestData::class, 'testDataHandler']);

            // Single route under '/example-one/route-b'
            $route->single('get', '/route-b', [TestData::class, 'testDataHandler']);
        });

        // Prefix with group and single routes
        $route->prefix('/example-two', function () use ($route)
        {
            // Single route under '/example-two/route-x'
            $route->single('get', '/route-x', [TestData::class, 'testDataHandler']);

            // Grouped routes with middleware
            $route->group(
                function () use ($route)
                {
                    // Single route under '/example-two/protected/route-y'
                    $route->single('get', '/protected/route-y', [TestData::class, 'testDataHandler']);

                    // Single route under '/example-two/protected/route-z'
                    $route->single('get', '/protected/route-z', [TestData::class, 'testDataHandler']);
                },
                [AuthMiddleware::class]
            );

            // Single route outside the group but still under '/example-two'
            $route->single('get', '/route-w', [TestData::class, 'testDataHandler']);
        });

        // Instantiate the other handler class to work non static methods
        $otherHandlers = new OtherHandlers();

        // Authenticated Routes
        $route->group(
            function () use ($route, $otherHandlers)
            {
                $route->single('get', '/secure-data', [$otherHandlers, 'getSecureData']);
                $route->single('post', '/secure-data', [$otherHandlers, 'postSecureData']);
                $route->single('post', '/file-upload', [$otherHandlers, 'fileUploadHandler']);
                $route->single('get', '/custom-response', [$otherHandlers, 'customResponseHandler']);
            },
            [CORSMiddleware::class, AuthUserPassMiddleware::class]
        );

        // Authentication with Handler Methods
        $route->single('post', '/auth/application-password', [$otherHandlers, 'handleApplicationPasswordAuth'], [PublicMiddleware::class]);
        $route->single('post', '/auth/username-password', [$otherHandlers, 'handleUsernamePasswordAuth'], [PublicMiddleware::class]);

        // Admin-Only Routes
        $route->group(
            function () use ($route, $otherHandlers)
            {
                $route->single('get', '/admin-only', [$otherHandlers, 'adminOnlyHandler']);
                $route->single('get', '/nested-admin-data', [$otherHandlers, 'nestedAdminDataHandler']);
            },
            [AuthMiddleware::class, RoleMiddleware::class]
        );

        // CRUD Operations
        $route->single('post', '/data/add', [$otherHandlers, 'addDataHandler'], [AuthMiddleware::class]);
        $route->single('put', '/data/edit', [$otherHandlers, 'editDataHandler'], [AuthMiddleware::class]);
        $route->single('delete', '/data/delete', [$otherHandlers, 'deleteDataHandler'], [AuthMiddleware::class]);
        $route->single('get', '/data/get', [$otherHandlers, 'getDataHandler'], [PublicMiddleware::class]);
        $route->single('get', '/data/list', [$otherHandlers, 'listDataHandler'], [AuthMiddleware::class]);
        $route->single('post', '/data/restore', [$otherHandlers, 'restoreDataHandler'], [AuthMiddleware::class]);
        $route->single('post', '/data/bulk-delete', [$otherHandlers, 'bulkDeleteHandler'], [AuthMiddleware::class]);
        $route->single('post', '/data/bulk-update', [$otherHandlers, 'bulkUpdateHandler'], [AuthMiddleware::class]);
        $route->single('post', '/data/duplicate', [$otherHandlers, 'duplicateDataHandler'], [AuthMiddleware::class]);

        // Export and Import Routes
        $route->single('get', '/data/export', [$otherHandlers, 'exportDataHandler'], [AuthMiddleware::class]);
        $route->single('post', '/data/import', [$otherHandlers, 'importDataHandler'], [AuthMiddleware::class]);

        // Rate-Limited Route
        $route->single('get', '/rate-limited-endpoint', [$otherHandlers, 'rateLimitedHandler'], [
            AuthMiddleware::class,
            RateLimitMiddleware::class,
        ]);

        // CORS-Enabled Route
        $route->single('get', '/cors-enabled-endpoint', [$otherHandlers, 'corsHandler'], [CORSMiddleware::class]);

        // Secure Admin Route with Multiple Middleware
        $route->single('get', '/secure-data/admin', [$otherHandlers, 'adminSecureDataHandler'], [
            AuthMiddleware::class,
            RoleMiddleware::class,
            CORSMiddleware::class,
        ]);

        // Deeply Nested Middleware and grouped routes
        $route->group(
            function () use ($route, $otherHandlers)
            {
                $route->group(
                    function () use ($route, $otherHandlers)
                    {
                        $route->single('get', '/nested-protected-endpoint', [$otherHandlers, 'nestedProtectedHandler'], [CORSMiddleware::class]);
                    },
                    [RoleMiddleware::class]
                );
            },
            [AuthMiddleware::class]
        );

        // Advanced Examples
        $route->single('get', '/data/search', [$otherHandlers, 'searchDataHandler'], [AuthMiddleware::class]);
        $route->single('get', '/data/history', [$otherHandlers, 'getDataHistoryHandler'], [AuthMiddleware::class]);
        $route->single('get', '/error-example', [$otherHandlers, 'errorExampleHandler'], [PublicMiddleware::class]);

    }
}
