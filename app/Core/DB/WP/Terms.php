<?php

namespace PluginFrame\Core\DB\WP;

use PluginFrame\Core\DB\Utils\QueryBuilder;
use PluginFrame\Core\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Terms
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'terms';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all terms with pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function allTerms($page = 1, $perPage = 10)
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
     * Get Term data for a specific Term with pagination.
     *
     * @param int $termId The Term ID to get data for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function singleTerm($termId, $page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder->table($this->table)->where('term_id', $termId),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->table($this->table)->where('term_id', $termId)->get();
    }

    /**
     * Get a specific term by its ID.
     *
     * @param int $termId The term ID.
     * @return mixed
     */
    public function getTerm($termId)
    {
        return $this->queryBuilder->table($this->table)->where('term_id', $termId)->get();
    }

    /**
     * Insert a new term.
     *
     * @param array $data The data to insert.
     * @return bool|int
     */
    public function insertTerm($data)
    {
        return $this->queryBuilder->table($this->table)->insert($data);
    }

    /**
     * Update a term by its ID.
     *
     * @param int $termId The term ID.
     * @param array $data The data to update.
     * @return bool
     */
    public function updateTerm($termId, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('term_id', $termId)
            ->update($data);
    }

    /**
     * Delete a term by its ID.
     *
     * @param int $termId The term ID.
     * @return bool
     */
    public function deleteTerm($termId)
    {
        return $this->queryBuilder->table($this->table)->where('term_id', $termId)->delete();
    }
}
