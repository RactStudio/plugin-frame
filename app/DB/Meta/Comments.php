<?php

namespace Pluginframe\DB\Meta;

use Pluginframe\DB\Utils\QueryBuilder;
use Pluginframe\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Comments
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'comments';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all comments with optional pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @param array $columns The items is selected (default is all `*`).
     * @return array
     */
    public function allComments($page = 1, $perPage = 10, $columns = ['*']): array
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder,
                $page,
                $perPage,
                $this->table,
                $columns,
            );
        } else {
            $result =  $this->queryBuilder->table($this->table)->select($columns)->get();
        }

        return [
            'data' => $result
        ];
    }

    /**
     * Get Comment data for a specific Comment with pagination.
     *
     * @param int $commentId The Comment ID to get data for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @param array $columns The items is selected (default is all `*`).
     * @return array
     */
    public function getComment($commentId, $page = 1, $perPage = 10, $columns = ['*']): array
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder,
                $page,
                $perPage,
                $this->table,
                $columns,
                ['comment_ID' => $commentId,],
            );
        } else {
            $result =  $this->queryBuilder->table($this->table)->select($columns)->where('comment_ID', $commentId)->get();
        }

        return [
            'data' => $result
        ];
    }

    /**
     * Get Post Comment data for a specific Post with pagination.
     *
     * @param int $postId The Post ID to get data for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @param array $columns The items is selected (default is all `*`).
     * @return array
     */
    public function getPostComment($postId, $page = 1, $perPage = 10, $columns = ['*']): array
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder,
                $page,
                $perPage,
                $this->table,
                $columns,
                ['post_id' => $postId,],
            );
        } else {
            $result =  $this->queryBuilder->table($this->table)->select($columns)->where('comment_ID', $commentId)->get();
        }

        return [
            'data' => $result
        ];
    }

    public function insertComment($data)
    {
        return $this->queryBuilder->table($this->table)->insert($data);
    }

    public function updateComment($commentId, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('comment_ID', $commentId)
            ->update($data);
    }

    public function deleteComment($commentId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('comment_ID', $commentId)
            ->delete();
    }
}
