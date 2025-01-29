<?php

namespace Pluginframe\WPDB\Meta;

use Pluginframe\DB\Utils\QueryBuilder;
use Pluginframe\DB\Pagination\PaginationManager;

/**
 * CommentsMeta class for interacting with wp_commentmeta table.
 */
class CommentsMeta
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
     * @return array
     */
    public function allCommentsMeta($page = 1, $perPage = 10)
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
     * Get Comment Metadata for a specific Comment with pagination.
     *
     * @param int $commentId The Comment ID to get metadata for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function singleCommentMeta($commentId, $page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder->table($this->table)->where('comment_id', $commentId),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->table($this->table)->where('comment_id', $commentId)->get();
    }

    public function getMeta($commentId, $metaKey)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('comment_id', $commentId)
            ->where('meta_key', $metaKey)
            ->get();
    }

    public function updateMeta($commentId, $metaKey, $metaValue)
    {
        return $this->queryBuilder
            ->table($this->table) 
            ->where('comment_id', $commentId)
            ->where('meta_key', $metaKey)
            ->update(['meta_value' => $metaValue]);
    }

    public function insertMeta($commentId, $metaKey, $metaValue)
    {
        return $this->queryBuilder
            ->table($this->table) 
            ->insert([
                'comment_id' => $commentId,
                'meta_key' => $metaKey,
                'meta_value' => $metaValue,
            ]);
    }

    public function deleteMeta($commentId, $metaKey)
    {
        return $this->queryBuilder
            ->table($this->table) 
            ->where('comment_id', $commentId)
            ->where('meta_key', $metaKey)
            ->delete();
    }

    public function getAllMeta($commentId)
    {
        return $this->queryBuilder
            ->table($this->table) 
            ->where('comment_id', $commentId)
            ->get();
    }
}
