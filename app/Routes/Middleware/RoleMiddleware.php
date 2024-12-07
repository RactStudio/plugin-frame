<?php

namespace PluginFrame\Routes\Middleware;

use WP_Error;

class RoleMiddleware
{
    public static function handle($request)
    {
        if (current_user_can('administrator')) {
            return true;
        }
        return new WP_Error('rest_forbidden', __('Permission denied.', 'plugin-frame'), ['status' => 403]);
    }
}
