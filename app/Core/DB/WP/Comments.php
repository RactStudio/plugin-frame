<?php

namespace PluginFrame\Core\DB\WP;

use PluginFrame\Core\DB\Utils\QueryBuilder;
use PluginFrame\Core\DB\Pagination\PaginationManager;

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
     * @param object $request The request object.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @param string $sortColumn The column to sort by - Default is `null`.
     * @param string $sortBy The sort direction ('asc' or 'desc').
     * @param array $columns The items is selected (default is all `*`).
     * @return array
     */
    public function allComments($request, $page = 1, $perPage = 10, $sortColumn = null, $sortBy = 'desc', $columns = ['*']): array
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder,
                $request,
                $page,
                $perPage,
                $sortColumn,
                $sortBy,
                $this->table,
                $columns,
            );
        } else {
            $result =  $this->queryBuilder->table($this->table)->orderBy($sortColumn, $sortBy)->select($columns)->get();
        }

        return [
            'data' => $result
        ];
    }

    /**
     * Get Comment data for a specific Comment with pagination.
     *
     * @param object $request The request object.
     * @param int $commentId The Comment ID to get data for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @param string $sortBy The 'asc' or 'desc' for sorting by comment_date.
     * @param array $columns The items is selected (default is all `*`).
     * @return array
     */
    public function getComment($request, $commentId, $page = 1, $perPage = 10, $sortColumn = null, $sortBy = 'desc', $columns = ['*']): array
    {
        $commentId = intval($request->get_param('comment_id')) ?: intval($commentId); // Default to comment_id param value (even if specified)
        
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder,
                $request,
                $page,
                $perPage,
                $sortColumn,
                $sortBy,
                $this->table,
                $columns,
                ['comment_ID' => $commentId,],
            );
        } else {
            $result =  $this->queryBuilder->table($this->table)->orderBy($sortColumn, $sortBy)->select($columns)->where('comment_ID', $commentId)->get();
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
    public function getPostComment($request, $postId, $page = 1, $perPage = 10, $sortColumn = null, $sortBy = 'desc', $columns = ['*']): array
    {
        $postId = intval($request->get_param('post_id')) ?: intval($postId); // Default to post_id param value (even if specified)
        
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder,
                $request,
                $page,
                $perPage,
                $sortColumn,
                $sortBy,
                $this->table,
                $columns,
                ['comment_post_ID' => $postId,],
            );
        } else {
            $result =  $this->queryBuilder->table($this->table)->orderBy($sortColumn, $sortBy)->select($columns)->where('comment_post_ID', $postId)->get();
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
