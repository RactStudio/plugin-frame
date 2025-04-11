<?php

namespace PluginFrame\Core\Routes\Middleware;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use WP_Error;

class AuthMiddleware
{
    public function handle($request)
    {
        $headers = getallheaders();

        // Check for Authorization Header
        if (empty($headers['Authorization'])) {
            return new WP_Error('missing_auth_header', __('Missing Authorization header.', 'plugin-frame'), ['status' => 401]);
        }

        // Decode Authorization Header
        $auth_header = $headers['Authorization'];
        if (strpos($auth_header, 'Basic ') !== 0) {
            return new WP_Error('invalid_auth_header', __('Invalid Authorization header format.', 'plugin-frame'), ['status' => 401]);
        }

        $auth_data = base64_decode(substr($auth_header, 6));
        list($username, $password) = explode(':', $auth_data);

        // Authenticate User (App Password or User Password)
        $user = wp_authenticate_application_password(null, $username, $password);
        if (is_wp_error($user)) {
            // If App Password authentication fails, try regular login
            $user = wp_authenticate($username, $password);
        }

        if (is_wp_error($user)) {
            return new WP_Error('invalid_credentials', __('Invalid credentials.', 'plugin-frame'), ['status' => 401]);
        }

        // Set current user (optional, for subsequent operations)
        wp_set_current_user(null, $user->user_login);

        return true; // Authentication passed
    }
}
