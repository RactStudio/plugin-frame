<?php

namespace PluginFrame\Core\DB\WP;

use PluginFrame\DB\Utils\QueryBuilder;
use PluginFrame\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Users
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'users';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all users with pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function allUsers($page = 1, $perPage = 10)
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
     * Get User data for a specific User with pagination.
     *
     * @param int $userId The User ID to get data for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function singleUser($userId, $page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder->table($this->table)->where('ID', $userId),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->table($this->table)->where('ID', $userId)->get();
    }

    /**
     * Get a specific user by ID.
     *
     * @param int $userId The user ID.
     * @return mixed
     */
    public function getUser($userId)
    {
        return $this->queryBuilder->table($this->table)->where('ID', $userId)->get();
    }

    /**
     * Insert a new user.
     *
     * @param array $data The user data to insert.
     * @return bool|int
     */
    public function insertUser($data)
    {
        return $this->queryBuilder->table($this->table)->insert($data);
    }

    /**
     * Update a user by their ID.
     *
     * @param int $userId The user ID.
     * @param array $data The data to update.
     * @return bool
     */
    public function updateUser($userId, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('ID', $userId)
            ->update($data);
    }

    /**
     * Delete a user by their ID.
     *
     * @param int $userId The user ID.
     * @return bool
     */
    public function deleteUser($userId)
    {
        return $this->queryBuilder->table($this->table)->where('ID', $userId)->delete();
    }
}
