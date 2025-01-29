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
     * Insert a new post.
     *
     * @param array $data The data to insert.
     * @return bool|int
     */
    public function insertPost($data)
    {
        $insert = $this->queryBuilder
                ->table($this->table)
                ->select(['*'])
                ->insert($data);
        
        return  $insert;
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
        $update = $this->queryBuilder
                ->table($this->table)
                ->select(['*'])
                ->where('ID', $postId)
                ->update($data);
        
        return  $update;
    }

    /**
     * Delete a post by its ID.
     *
     * @param int $postId The post ID.
     * @return bool
     */
    public function deletePost($postId)
    {
        $delete = $this->queryBuilder
                ->table($this->table)
                ->select(['*'])
                ->where('ID', $postId)
                ->delete();

        return  $delete;
    }
}
