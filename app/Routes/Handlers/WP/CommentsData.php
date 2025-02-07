<?php

namespace PluginFrame\Routes\Handlers\WP;

use Exception;
use WP_REST_Response;
use PluginFrame\DB\WP\Comments;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class CommentsData
{
    /**
     * allComments
     * [root-domain]/wp-json/plugin-frame/v1/comments/all/?page=1&per_page=10
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function getAllComments($request)
    {
        try {
            // Get pagination parameters from the request
            $page = 1; // Default to page 1 if not specified
            $perPage = 5; // Default to 10 items per page
            $sortBy = 'desc'; // Default to 'desc' Date (if not specified)
        
            // Fetch comments
            $response = (new Comments())->allComments($request, $page, $perPage, $sortBy);

            return rest_ensure_response($response);
        } catch (Exception $e) {
            return new WP_REST_Response(
                ['error' => 'Failed to fetch comments', 'message' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * allComments
     * [root-domain]/wp-json/plugin-frame/v1/comments/all/?page=1&per_page=10
     * 
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function getComment($request)
    {
        try {
            // Get pagination parameters from the request
            $page = intval($request->get_param('page')) ?: 1; // Default to page 1 if not specified
            $perPage = intval($request->get_param('per_page')) ?: 10; // Default to 10 items per page

            // Fetch comments
            $response = (new Comments())->allComments($page, $perPage);

            return rest_ensure_response($response);
        } catch (Exception $e) {
            return new WP_REST_Response(
                ['error' => 'Failed to fetch comments', 'message' => $e->getMessage()],
                500
            );
        }
    }

}
