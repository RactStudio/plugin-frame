<?php

namespace PluginFrame\Core\DB\WP;

use PluginFrame\Core\DB\Utils\QueryBuilder;
use PluginFrame\Core\DB\Pagination\PaginationManager;

/**
 * CommentsMeta class for interacting with wp_commentmeta table.
 */
class CommentMeta
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'commentmeta';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all comment meta with optional pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @param array $columns The items is selected (default is all `*`).
     * @return array
     */
    public function allCommentsMeta($page = 1, $perPage = 10, $columns = ['*'])
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
     * Get Comment Metadata for a specific Comment with pagination.
     *
     * @param int $commentId The Comment ID to get metadata for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @param array $columns The items is selected (default is all `*`).
     * @return array
     */
    public function getCommentMeta($commentId, $page = 1, $perPage = 10, $columns = ['*'])
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder,
                $page,
                $perPage,
                $this->table,
                $columns,
                ['comment_id' => $commentId,],
            );
        } else {
            $result =  $this->queryBuilder->table($this->table)->select($columns)->where('comment_id', $commentId)->get();
        }

        return [
            'data' => $result
        ];
    }

    /**
     * Get Post Comment Metadata for a specific Post with pagination.
     *
     * @param int $postId The Post ID to get metadata for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @param array $columns The items is selected (default is all `*`).
     * @return array
     */
    public function getPostCommentMeta($postId, $page = 1, $perPage = 10, $columns = ['*'])
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
            $result =  $this->queryBuilder->table($this->table)->select($columns)->where('post_id', $postId)->get();
        }

        return [
            'data' => $result
        ];
    }

    public function insertCommentMeta($commentId, $metaKey, $metaValue)
    {
        return $this->queryBuilder
            ->table($this->table) 
            ->insert([
                'comment_id' => $commentId,
                'meta_key' => $metaKey,
                'meta_value' => $metaValue,
            ]);
    }

    public function updateCommentMeta($commentId, $metaKey, $metaValue)
    {
        return $this->queryBuilder
            ->table($this->table) 
            ->where('comment_id', $commentId)
            ->where('meta_key', $metaKey)
            ->update(['meta_value' => $metaValue]);
    }

    public function deleteCommentMeta($commentId, $metaKey)
    {
        return $this->queryBuilder
            ->table($this->table) 
            ->where('comment_id', $commentId)
            ->where('meta_key', $metaKey)
            ->delete();
    }
}
