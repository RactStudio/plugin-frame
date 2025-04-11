<?php

namespace PluginFrame\Core\DB\WP;

use PluginFrame\Core\DB\Utils\QueryBuilder;
use PluginFrame\Core\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class TermMeta
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'termmeta';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all term metadata with pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function allTermMeta($page = 1, $perPage = 10)
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
     * Get metadata for a specific term by its ID.
     *
     * @param int $termId The term ID.
     * @return mixed
     */
    public function getTermMeta($termId)
    {
        return $this->queryBuilder->table($this->table)->where('term_id', $termId)->get();
    }

    /**
     * Insert new metadata for a term.
     *
     * @param int $termId The term ID.
     * @param string $metaKey The metadata key.
     * @param mixed $metaValue The metadata value.
     * @return bool|int
     */
    public function insertTermMeta($termId, $metaKey, $metaValue)
    {
        return $this->queryBuilder->table($this->table)->insert([
            'term_id' => $termId,
            'meta_key' => $metaKey,
            'meta_value' => $metaValue,
        ]);
    }

    /**
     * Update metadata for a specific term.
     *
     * @param int $termId The term ID.
     * @param string $metaKey The metadata key.
     * @param mixed $metaValue The metadata value.
     * @return bool
     */
    public function updateTermMeta($termId, $metaKey, $metaValue)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('term_id', $termId)
            ->where('meta_key', $metaKey)
            ->update(['meta_value' => $metaValue]);
    }

    /**
     * Delete metadata for a term.
     *
     * @param int $termId The term ID.
     * @param string $metaKey The metadata key.
     * @return bool
     */
    public function deleteTermMeta($termId, $metaKey)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('term_id', $termId)
            ->where('meta_key', $metaKey)
            ->delete();
    }
}
