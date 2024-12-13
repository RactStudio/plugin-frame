<?php

namespace PluginFrame\Routes\Middleware;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use WP_Error;

class AuthUserPassMiddleware
{
    public function handle($request)
    {

        $auth_header = $request->get_header('Authorization');
        
        if (!$auth_header) {
            return new WP_Error('missing_auth_header', 'Authorization header is required', ['status' => 401]);
        }
    
        if (strpos($auth_header, 'Basic ') === 0) {
            $credentials = base64_decode(substr($auth_header, 6));
            
            list($username, $password) = explode(':', $credentials);
    
            if (!$username || !$password) {
                return new WP_Error('invalid_credentials', 'Invalid credentials provided', ['status' => 401]);
            }
    
            $user = wp_authenticate($username, $password);
    
            if (is_wp_error($user)) {
                return new WP_Error('auth_failed', 'Invalid username or password', ['status' => 401]);
            }
    
            // Set current user (optional, for subsequent operations)
            wp_set_current_user(null, $user->user_login);
            
            // return rest_ensure_response(['message' => 'Authentication successful', 'user' => $user->user_login]);
            return true; // Authentication passed
        }
    
        return new WP_Error('invalid_auth_method', 'Invalid authentication method', ['status' => 400]);
    }
}
