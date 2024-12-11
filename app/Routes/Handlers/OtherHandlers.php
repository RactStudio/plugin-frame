<?php

namespace PluginFrame\Routes\Handlers;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class OtherHandlers
{
    // Authenticated endpoint
    public function getSecureData($request)
    {
        return rest_ensure_response(['data' => 'This is secure data']);
    }

    public function handleApplicationPasswordAuth($request)
    {
        // Get the Authorization header
        $auth_header = $request->get_header('Authorization');
    
        if (!$auth_header) {
            return new \WP_Error('missing_auth_header', 'Authorization header is required', ['status' => 401]);
        }
    
        // Decode the base64 credentials from the header
        if (strpos($auth_header, 'Basic ') === 0) {
            $credentials = base64_decode(substr($auth_header, 6));
    
            list($username, $application_password) = explode(':', $credentials);
    
            if (!$username || !$application_password) {
                return new \WP_Error('invalid_credentials', 'Invalid credentials provided', ['status' => 401]);
            }
    
            // Authenticate using WordPress's auth function
            $user = wp_authenticate_application_password($application_password);
    
            if (is_wp_error($user)) {
                return new \WP_Error('auth_failed', 'Invalid application password', ['status' => 401]);
            }
    
            return rest_ensure_response(['message' => 'Authentication successful', 'user' => $user->user_login]);
        }
    
        return new \WP_Error('invalid_auth_method', 'Invalid authentication method', ['status' => 400]);
    }    
    
    public function handleUsernamePasswordAuth($request)
    {
        $auth_header = $request->get_header('Authorization');
    
        if (!$auth_header) {
            return new \WP_Error('missing_auth_header', 'Authorization header is required', ['status' => 401]);
        }
    
        if (strpos($auth_header, 'Basic ') === 0) {
            $credentials = base64_decode(substr($auth_header, 6));
    
            list($username, $password) = explode(':', $credentials);
    
            if (!$username || !$password) {
                return new \WP_Error('invalid_credentials', 'Invalid credentials provided', ['status' => 401]);
            }
    
            $user = wp_authenticate($username, $password);
    
            if (is_wp_error($user)) {
                return new \WP_Error('auth_failed', 'Invalid username or password', ['status' => 401]);
            }
    
            return rest_ensure_response(['message' => 'Authentication successful', 'user' => $user->user_login]);
        }
    
        return new \WP_Error('invalid_auth_method', 'Invalid authentication method', ['status' => 400]);
    }    
    
    public function postSecureData($request)
    {
        $data = $request->get_json_params();
        return rest_ensure_response(['message' => 'Data received', 'data' => $data]);
    }

    public function fileUploadHandler($request)
    {
        return rest_ensure_response(['message' => 'File uploaded']);
    }

    public function customResponseHandler($request)
    {
        return rest_ensure_response(['custom' => 'This is a custom response']);
    }

    public function adminOnlyHandler($request)
    {
        return rest_ensure_response(['message' => 'Admin only']);
    }

    public function nestedAdminDataHandler($request)
    {
        return rest_ensure_response(['message' => 'Nested admin data']);
    }

    public function addDataHandler($request)
    {
        return rest_ensure_response(['message' => 'Data added']);
    }

    public function editDataHandler($request)
    {
        return rest_ensure_response(['message' => 'Data updated']);
    }

    public function deleteDataHandler($request)
    {
        return rest_ensure_response(['message' => 'Data deleted']);
    }

    public function getDataHandler($request)
    {
        return rest_ensure_response(['data' => 'Fetched data']);
    }

    public function listDataHandler($request)
    {
        return rest_ensure_response(['data' => 'List of data']);
    }

    public function restoreDataHandler($request)
    {
        return rest_ensure_response(['message' => 'Data restored']);
    }

    public function bulkDeleteHandler($request)
    {
        return rest_ensure_response(['message' => 'Bulk delete successful']);
    }

    public function bulkUpdateHandler($request)
    {
        return rest_ensure_response(['message' => 'Bulk update successful']);
    }

    public function duplicateDataHandler($request)
    {
        return rest_ensure_response(['message' => 'Data duplicated']);
    }

    public function exportDataHandler($request)
    {
        return rest_ensure_response(['message' => 'Data exported']);
    }

    public function importDataHandler($request)
    {
        return rest_ensure_response(['message' => 'Data imported']);
    }

    public function searchDataHandler($request)
    {
        return rest_ensure_response(['data' => 'Search results']);
    }

    public function getDataHistoryHandler($request)
    {
        return rest_ensure_response(['data' => 'Change history']);
    }

    public function errorExampleHandler($request)
    {
        return new \WP_Error('error_code', __('This is an error response', 'plugin-frame'), ['status' => 400]);
    }
}
