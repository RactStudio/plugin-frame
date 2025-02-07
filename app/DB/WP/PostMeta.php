<?php

namespace PluginFrame\DB\WP;

use Pluginframe\DB\Utils\QueryBuilder;
use Pluginframe\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

/**
 * PostMeta class for interacting with wp_postmeta table.
 */
class PostMeta
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'postmeta';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all Post Metadata.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @param array $columns The items is selected.
     * @return array
     */
    public function allPostMeta($page = 1, $perPage = 10, $columns = ['*']): array
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder,
                $page,
                $perPage,
                $this->table,
                [$columns],
            );
        } else {
            $result = $this->queryBuilder->table($this->table)->select($columns)->get();
        }

        return [
            'data' => $result
        ];
    }

    /**
     * Get Post Metadata for a specific Post with pagination.
     *
     * @param int $postId The Post ID to get metadata for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @param array $columns The items is selected (default is all `*`).
     * @return array
     */
    public function getPostMeta($postId, $page = 1, $perPage = 10, $columns = ['*']): array
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder,
                $page,
                $perPage,
                $this->table,
                [$columns],
                ['post_id' => $postId,],
            );
        } else {
            $result = $this->queryBuilder->table($this->table)->select($columns)->where('post_id', $postId)->get();
        }

        return [
            'data' => $result
        ];
    }

    /**
     * Get metadata for a post by key.
     *
     * @param int $postId The Post ID.
     * @param string $metaKey The metadata key.
     * @param array $columns The items is selected (default is all `*`).
     * @return array
     */
    public function singlePostMeta($postId, $metaKey, $columns = ['*']): array
    {
        $result = $this->queryBuilder
                ->table($this->table)
                ->select($columns)
                ->where('post_id', $postId)
                ->where('meta_key', $metaKey)
                ->get();

        return [
            'data' => $result
        ];
    }

    /**
     * Insert post metadata.
     *
     * @param int $postId The Post ID.
     * @param string $metaKey The metadata key.
     * @param mixed $metaValue The metadata value.
     * @return bool|int Meta ID or false on failure.
     */
    public function insertPostMeta($postId, $metaKey, $metaValue)
    {
        return update_post_meta($postId, $metaKey, $metaValue);
    }

    /**
     * Update post metadata.
     *
     * @param int $postId The Post ID.
     * @param string $metaKey The metadata key.
     * @param mixed $metaValue The metadata value.
     * @return bool|int Meta ID or false on failure.
     */
    public function updatePostMeta($postId, $metaKey, $metaValue)
    {
        return update_post_meta($postId, $metaKey, $metaValue);
    }

    /**
     * Delete metadata for a post.
     *
     * @param int $postId The Post ID.
     * @param string $metaKey The metadata key.
     * @return bool True on success, false on failure.
     */
    public function deleteMeta($postId, $metaKey)
    {
        return delete_post_meta($postId, $metaKey);
    }
}
