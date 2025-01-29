<?php

namespace Pluginframe\WPDB\Meta;

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
     * @return array
     */
    public function allPostMeta($page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder->table($this->table),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->table($this->table)->get();
    }

    /**
     * Get Post Metadata for a specific Post with pagination.
     *
     * @param int $postId The Post ID to get metadata for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function singlePostMeta($postId, $page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder->table($this->table)->where('post_id', $postId),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->table($this->table)->where('post_id', $postId)->get();
    }

    /**
     * Get metadata for a post by key.
     *
     * @param int $postId The Post ID.
     * @param string $metaKey The metadata key.
     * @return mixed
     */
    public function getMeta($postId, $metaKey)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('post_id', $postId)
            ->where('meta_key', $metaKey)
            ->get();
    }

    /**
     * Add or update metadata for a post.
     *
     * @param int $postId The Post ID.
     * @param string $metaKey The metadata key.
     * @param mixed $metaValue The metadata value.
     * @return bool
     */
    public function updateMeta($postId, $metaKey, $metaValue)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('post_id', $postId)
            ->where('meta_key', $metaKey)
            ->update(['meta_value' => $metaValue]);
    }

    /**
     * Insert new post metadata.
     *
     * @param int $postId The Post ID.
     * @param string $metaKey The metadata key.
     * @param mixed $metaValue The metadata value.
     * @return bool|int
     */
    public function insertMeta($postId, $metaKey, $metaValue)
    {
        return $this->queryBuilder->table($this->table)->insert([
            'post_id' => $postId,
            'meta_key' => $metaKey,
            'meta_value' => $metaValue,
        ]);
    }

    /**
     * Delete metadata for a post by key.
     *
     * @param int $postId The Post ID.
     * @param string $metaKey The metadata key.
     * @return bool
     */
    public function deleteMeta($postId, $metaKey)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('post_id', $postId)
            ->where('meta_key', $metaKey)
            ->delete();
    }
}
