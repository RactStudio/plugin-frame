<?php

namespace Pluginframe\WPDB\Meta;

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
                $this->queryBuilder->table($this->table),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->table($this->table)->get();
    }

    /**
     * Get a specific post by its ID.
     *
     * @param int $postId The post ID.
     * @return mixed
     */
    public function getPost($postId)
    {
        return $this->queryBuilder->table($this->table)->where('ID', $postId)->get();
    }

    /**
     * Insert a new post.
     *
     * @param array $data The data to insert.
     * @return bool|int
     */
    public function insertPost($data)
    {
        return $this->queryBuilder->table($this->table)->insert($data);
    }

    /**
     * Update a post by its ID.
     *
     * @param int $postId The post ID.
     * @param array $data The data to update.
     * @return bool
     */
    public function updatePost($postId, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('ID', $postId)
            ->update($data);
    }

    /**
     * Delete a post by its ID.
     *
     * @param int $postId The post ID.
     * @return bool
     */
    public function deletePost($postId)
    {
        return $this->queryBuilder->table($this->table)->where('ID', $postId)->delete();
    }
}
