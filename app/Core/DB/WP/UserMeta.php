<?php

namespace PluginFrame\Core\DB\WP;

use PluginFrame\Core\DB\Utils\QueryBuilder;
use PluginFrame\Core\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class UserMeta
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'usermeta';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all user metadata with pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function allUserMeta($page = 1, $perPage = 10)
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
     * Get User Metadata for a specific User with pagination.
     *
     * @param int $userId The User ID to get metadata for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function singleUserMeta($userId, $page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder->table($this->table)->where('user_id', $userId),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->table($this->table)->where('user_id', $userId)->get();
    }

    /**
     * Get metadata for a specific user by user ID.
     *
     * @param int $userId The user ID.
     * @return mixed
     */
    public function getUserMeta($userId)
    {
        return $this->queryBuilder->table($this->table)->where('user_id', $userId)->get();
    }

    /**
     * Insert new metadata for a user.
     *
     * @param int $userId The user ID.
     * @param string $metaKey The metadata key.
     * @param mixed $metaValue The metadata value.
     * @return bool|int
     */
    public function insertUserMeta($userId, $metaKey, $metaValue)
    {
        return $this->queryBuilder->table($this->table)->insert([
            'user_id' => $userId,
            'meta_key' => $metaKey,
            'meta_value' => $metaValue,
        ]);
    }

    /**
     * Update metadata for a user.
     *
     * @param int $userId The user ID.
     * @param string $metaKey The metadata key.
     * @param mixed $metaValue The metadata value.
     * @return bool
     */
    public function updateUserMeta($userId, $metaKey, $metaValue)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('user_id', $userId)
            ->where('meta_key', $metaKey)
            ->update(['meta_value' => $metaValue]);
    }

    /**
     * Delete metadata for a user by key.
     *
     * @param int $userId The user ID.
     * @param string $metaKey The metadata key.
     * @return bool
     */
    public function deleteUserMeta($userId, $metaKey)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('user_id', $userId)
            ->where('meta_key', $metaKey)
            ->delete();
    }
}
