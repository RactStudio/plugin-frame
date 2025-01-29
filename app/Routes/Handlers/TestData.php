<?php

namespace PluginFrame\Routes\Handlers;

use PluginFrame\DB\Meta\PostMeta;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class TestData
{
    // Public endpoint display
    public static function testDBposts($request)
    {
        // [wordpress root]/wp-json/plugin-frame/v1/db-posts?page=1&per_page=10

        // Get pagination parameters from the request
        $page = intval($request->get_param('page')) ?: 1; // Default to page 1 if not specified
        $perPage = intval($request->get_param('per_page')) ?: 5; // Default to 10 items per page
        $post_id = 	25;
        $meta_key = '_edit_lock';

        $response = (new PostMeta())->getMeta($post_id, $meta_key);

        return rest_ensure_response($response);

    }

    // Public endpoint display posts and comments with pagination
    public static function testDataHandler($request)
    {
        // [wordpress root]/wp-json/plugin-frame/v1/[route]?page=1&per_page=10

        // Get pagination parameters from the request
        $page = intval($request->get_param('page')) ?: 1; // Default to page 1 if not specified
        $per_page = intval($request->get_param('per_page')) ?: 10; // Default to 10 items per page

        // Fetch posts with pagination
        $posts = get_posts([
            'numberposts' => $per_page, // Limit posts per page
            'offset' => ($page - 1) * $per_page, // Calculate offset based on current page
            'post_status' => 'publish',
        ]);

        // Fetch comments with pagination
        $comments = get_comments([
            'status' => 'approve',
            'number' => $per_page,
            'offset' => ($page - 1) * $per_page,
        ]);

        // Structure the response
        $response = [
            'message' => 'Damn, this is a public endpoint with pagination.',
            'pagination' => [
                'page' => $page,
                'per_page' => $per_page,
            ],
            'posts' => $posts,
            'comments' => $comments,
        ];

        return rest_ensure_response($response);
    }

}
