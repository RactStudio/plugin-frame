<?php

namespace Pluginframe\WPDB\Meta;

use Pluginframe\DB\Utils\QueryBuilder;
use Pluginframe\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class TermRelationships
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'term_relationships';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all term relationships with pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function allTermRelationships($page = 1, $perPage = 10)
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
     * Get term relationships for a specific object (e.g., post).
     *
     * @param int $objectId The object ID (e.g., post ID).
     * @return mixed
     */
    public function getTermRelationships($objectId)
    {
        return $this->queryBuilder->table($this->table)->where('object_id', $objectId)->get();
    }

    /**
     * Insert a new term relationship.
     *
     * @param int $objectId The object ID (e.g., post ID).
     * @param int $termTaxonomyId The term taxonomy ID.
     * @return bool|int
     */
    public function insertTermRelationship($objectId, $termTaxonomyId)
    {
        return $this->queryBuilder->table($this->table)->insert([
            'object_id' => $objectId,
            'term_taxonomy_id' => $termTaxonomyId,
        ]);
    }

    /**
     * Delete a term relationship by object ID and term taxonomy ID.
     *
     * @param int $objectId The object ID.
     * @param int $termTaxonomyId The term taxonomy ID.
     * @return bool
     */
    public function deleteTermRelationship($objectId, $termTaxonomyId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('object_id', $objectId)
            ->where('term_taxonomy_id', $termTaxonomyId)
            ->delete();
    }
}
