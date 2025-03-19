<?php

namespace PluginFrame\REST\Controllers\WP;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

use Exception;
use WP_REST_Response;
use PluginFrame\Core\DB\WP\Comments;

class CommentsData
{
    /**
     * allComments
     * [root-domain]/wp-json/plugin-frame/v1/comments/all/?page=1&per_page=10
     * 
     * @param WP_REST_Request $request
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @param string $sortColumn The column to sort by - Default is `null`.
     * @param string $sortBy The sort direction ('asc' or 'desc').
     * @return WP_REST_Response
     */
    public static function getAllComments($request)
    {
        try {
            // Get pagination parameters from the request
            $page = 1; // Default to page 1 if not specified
            $perPage = 8; // Default to 10 items per page
            $sortColumn = 'comment_date'; // Default to 'comment_date' (if not specified)
            $sortBy = 'desc'; // Default to 'desc' Date (if not specified)
        
            // Fetch comments
            $response = (new Comments())->allComments($request, $page, $perPage, $sortColumn, $sortBy);

            return rest_ensure_response($response);
        } catch (Exception $e) {
            return new WP_REST_Response(
                ['error' => 'Failed to fetch comments', 'message' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * getComment
     * [root-domain]/wp-json/plugin-frame/v1/comments/single/?comment_id=11
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function getComment($request)
    {
        try {
            // Get pagination parameters from the request
            $page = 1; // Default to page 1 if not specified
            $perPage = 8; // Default to 10 items per page
            $commentId = 11;

            // Fetch comment
            $response = (new Comments())->getComment($request, $commentId, $page, $perPage);

            return rest_ensure_response($response);
        } catch (Exception $e) {
            return new WP_REST_Response(
                ['error' => 'Failed to fetch comment', 'message' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * getPostComment
     * [root-domain]/wp-json/plugin-frame/v1/comments/post/?post_id=1
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public static function getPostComment($request)
    {
        try {
            // Get pagination parameters from the request
            $page = 1; // Default to page 1 if not specified
            $perPage = 8; // Default to 10 items per page
            $postId = 1;
            $sortColumn = 'comment_date'; // Default to 'comment_date' (if not specified);
            $sortby = 'desc'; // Default to 'desc' Date (if not specified)

            // Fetch comment
            $response = (new Comments())->getPostComment($request, $postId, $page, $perPage, $sortColumn, $sortby);

            return rest_ensure_response($response);
        } catch (Exception $e) {
            return new WP_REST_Response(
                ['error' => 'Failed to fetch comment', 'message' => $e->getMessage()],
                500
            );
        }
    }

}
