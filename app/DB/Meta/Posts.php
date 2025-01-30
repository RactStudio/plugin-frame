<?php

namespace Pluginframe\DB\Meta;

use Pluginframe\DB\Utils\QueryBuilder;
use Pluginframe\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Posts
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'posts';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all posts with pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function allPosts($page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder,
                $page,
                $perPage,
                $this->table,
            );
        } else {
            $result = $this->queryBuilder->table($this->table)->select(['*'])->get();
        }

        return [
            'data' => $result
        ];
    }

    /**
     * Get a specific post by its ID.
     *
     * @param int $postId The post ID.
     * @return mixed
     */
    public function getPost($postId)
    {
        $result= $this->queryBuilder->table($this->table)->select(['*'])->where('ID', $postId)->execute();
        
        return [ 
            'data' => $result
        ];
    }

    /**
     * Insert a new post with optional metadata.
     *
     * @param array $postData The post data.
     * @param array $metaData Optional metadata.
     * @return int|WP_Error Post ID or error.
     */
    public function insertPost($postData, $metaData = [])
    {
        $postId = wp_insert_post($postData);

        if (!is_wp_error($postId) && !empty($metaData)) {
            foreach ($metaData as $key => $value) {
                update_post_meta($postId, $key, $value);
            }
        }

        return $postId;
    }

    /**
     * Update a post and its metadata.
     *
     * @param int $postId Post ID.
     * @param array $postData The post data.
     * @param array $metaData Optional metadata updates.
     * @return bool|WP_Error True on success, WP_Error on failure.
     */
    public function updatePost($postId, $postData, $metaData = [])
    {
        $postData['ID'] = $postId;
        $updated = wp_update_post($postData);

        if (!is_wp_error($updated) && !empty($metaData)) {
            foreach ($metaData as $key => $value) {
                update_post_meta($postId, $key, $value);
            }
        }

        return !is_wp_error($updated);
    }

    /**
     * Delete a post and its metadata.
     *
     * @param int $postId Post ID.
     * @return bool True on success, false on failure.
     */
    public function deletePost($postId)
    {
        global $wpdb;

        // Delete post
        $deleted = wp_delete_post($postId, true);

        if ($deleted) {
            // Delete all metadata related to this post
            $wpdb->delete($wpdb->postmeta, ['post_id' => $postId]);
        }

        return (bool) $deleted;
    }
}
