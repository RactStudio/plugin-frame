<?php

namespace Pluginframe\WPDB\Meta;

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
     * @return array
     */
    public function allComments($page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder->table($this->table),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->get();
    }

    /**
     * Get Comment data for a specific Comment with pagination.
     *
     * @param int $commentId The Comment ID to get data for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function singleComment($commentId, $page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder->table($this->table)->where('comment_ID', $commentId),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->table($this->table)->where('comment_ID', $commentId)->get();
    }

    public function getComment($commentId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('comment_ID', $commentId)
            ->get();
    }

    public function updateComment($commentId, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('comment_ID', $commentId)
            ->update($data);
    }

    public function insertComment($data)
    {
        return $this->queryBuilder->table($this->table)->insert($data);
    }

    public function deleteComment($commentId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('comment_ID', $commentId)
            ->delete();
    }
}
