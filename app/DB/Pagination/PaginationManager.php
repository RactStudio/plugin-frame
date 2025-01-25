<?php

namespace Pluginframe\DB\Pagination;

use Pluginframe\DB\Utils\QueryBuilder;
use WP_Query;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

/**
 * PaginationManager handles the pagination logic for any database-related query.
 */
class PaginationManager
{
    /**
     * Get paginated results for any query.
     *
     * @param QueryBuilder $queryBuilder The query builder instance.
     * @param int $page The current page number.
     * @param int $perPage The number of items per page.
     * @param string $table The table name for the query.
     * @param array $select The columns to select.
     * @param array $where The WHERE conditions.
     * @param bool $countQuery Whether to perform a count query.
     * @return array The results or the total record count.
     */
    public function getPaginatedResults(QueryBuilder $queryBuilder, $page = 1, $perPage = 10, $table = '', $select = [], $where = [], $countQuery = false)
    {
        // Set the query parameters
        $queryBuilder->table($table);
        $queryBuilder->select($select);

        // Add where conditions if provided
        $this->applyWhereConditions($queryBuilder, $where);

        // Set pagination parameters
        $this->applyPagination($queryBuilder, $page, $perPage);

        // Return either the paginated results or the count based on $countQuery
        return $countQuery ? $this->getTotalRecords($queryBuilder) : $this->fetchResultsWithMetadata($queryBuilder, $page, $perPage);
    }

    /**
     * Apply WHERE conditions to the query.
     *
     * @param QueryBuilder $queryBuilder
     * @param array $where The WHERE conditions.
     */
    private function applyWhereConditions(QueryBuilder $queryBuilder, array $where)
    {
        foreach ($where as $condition) {
            $queryBuilder->where($condition[0], $condition[1], $condition[2]);
        }
    }

    /**
     * Apply pagination (LIMIT and OFFSET) to the query.
     *
     * @param QueryBuilder $queryBuilder
     * @param int $page The current page number.
     * @param int $perPage The number of items per page.
     */
    private function applyPagination(QueryBuilder $queryBuilder, $page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        $queryBuilder->limit($perPage);
        $queryBuilder->offset($offset);
    }

    /**
     * Fetch the results and include pagination metadata.
     *
     * @param QueryBuilder $queryBuilder
     * @param int $page The current page number.
     * @param int $perPage The number of items per page.
     * @return array The results with pagination metadata.
     */
    private function fetchResultsWithMetadata(QueryBuilder $queryBuilder, $page, $perPage)
    {
        // Get the results
        $results = $queryBuilder->get();

        // Get the total number of records for pagination metadata
        $totalRecords = $this->getTotalRecords($queryBuilder);

        // Calculate total pages
        $totalPages = ceil($totalRecords / $perPage);

        return [
            'data' => $results, // Paginated results
            'pagination' => $this->generatePaginationMetadata($page, $totalRecords, $perPage, $totalPages)
        ];
    }

    /**
     * Generate the pagination metadata.
     *
     * @param int $page The current page number.
     * @param int $totalRecords The total number of records.
     * @param int $perPage The number of items per page.
     * @param int $totalPages The total number of pages.
     * @return array The pagination metadata.
     */
    private function generatePaginationMetadata($page, $totalRecords, $perPage, $totalPages)
    {
        return [
            'total_records' => $totalRecords,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_next_page' => $page < $totalPages,
            'has_previous_page' => $page > 1,
        ];
    }

    /**
     * Get the total number of records for the current query.
     *
     * @param QueryBuilder $queryBuilder
     * @return int The total number of records.
     */
    private function getTotalRecords(QueryBuilder $queryBuilder)
    {
        return $queryBuilder->count();
    }

    /**
     * Generate pagination links.
     *
     * @param int $currentPage The current page number.
     * @param int $totalPages The total number of pages.
     * @return string The HTML for the pagination links.
     */
    public function generatePaginationLinks($currentPage, $totalPages)
    {
        // Generate the pagination links using WordPress's paginate_links function
        return paginate_links([
            'total' => $totalPages,
            'current' => $currentPage,
            'base' => '?paged=%#%',
        ]);
    }
}
