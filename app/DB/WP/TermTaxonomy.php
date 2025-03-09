<?php

namespace PluginFrame\DB\WP;

use PluginFrame\DB\Utils\QueryBuilder;
use PluginFrame\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class TermTaxonomy
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'term_taxonomy';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all term taxonomy records with pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function allTermTaxonomy($page = 1, $perPage = 10)
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
     * Get Term Taxonomy data for a specific Term Taxonomy with pagination.
     *
     * @param int $termTaxonomyId The Term Taxonomy ID to get data for.
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function singleTermTaxonomy($termTaxonomyId, $page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder->table($this->table)->where('term_taxonomy_id', $termTaxonomyId),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->table($this->table)->where('term_taxonomy_id', $termTaxonomyId)->get();
    }

    /**
     * Get a specific term taxonomy by its ID.
     *
     * @param int $termTaxonomyId The term taxonomy ID.
     * @return mixed
     */
    public function getTermTaxonomy($termTaxonomyId)
    {
        return $this->queryBuilder->table($this->table)->where('term_taxonomy_id', $termTaxonomyId)->get();
    }

    /**
     * Insert a new term taxonomy record.
     *
     * @param array $data The data to insert.
     * @return bool|int
     */
    public function insertTermTaxonomy($data)
    {
        return $this->queryBuilder->table($this->table)->insert($data);
    }

    /**
     * Update a term taxonomy by its ID.
     *
     * @param int $termTaxonomyId The term taxonomy ID.
     * @param array $data The data to update.
     * @return bool
     */
    public function updateTermTaxonomy($termTaxonomyId, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('term_taxonomy_id', $termTaxonomyId)
            ->update($data);
    }

    /**
     * Delete a term taxonomy by its ID.
     *
     * @param int $termTaxonomyId The term taxonomy ID.
     * @return bool
     */
    public function deleteTermTaxonomy($termTaxonomyId)
    {
        return $this->queryBuilder->table($this->table)->where('term_taxonomy_id', $termTaxonomyId)->delete();
    }
}
