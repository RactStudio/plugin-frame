<?php

namespace Pluginframe\DB\Pagination;

use Exception;
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
     * @param array $select The columns to select (default is all `*`).
     * @param array $where The WHERE conditions.
     * @param bool $countQuery Whether to perform a count query.
     * @return array The results or the total record count.
     * @throws Exception If the table name is missing.
     */
    public function getPaginatedResults(QueryBuilder $queryBuilder, $page = 1, $perPage = 10, $table = '', $select = '*', $where = [])
    {
        if (empty($table)) {
            throw new Exception('Table name must be specified for pagination.');
        }

        // Set the query table and columns
        $queryBuilder->table($table);
        $queryBuilder->select($select);

        // Apply WHERE conditions if provided
        $this->applyWhereConditions($queryBuilder, $where);

        // Apply pagination (LIMIT, OFFSET)
        $this->applyPagination($queryBuilder, $page, $perPage);

        // Fetch paginated results
        return $this->fetchResultsWithMetadata($queryBuilder, $page, $perPage);
    }

    /**
     * Apply WHERE conditions to the query.
     *
     * @param QueryBuilder $queryBuilder
     * @param array $where The WHERE conditions.
     */
    private function applyWhereConditions(QueryBuilder $queryBuilder, array $where)
    {
        foreach ($where as $key => $value) {
            $queryBuilder->where($key, $value);
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
        $links = $this->generatePrevNextLinksParam($page, $perPage, $totalPages);

        return [
            'total_records'     => $totalRecords,
            'per_page'          => $perPage,
            'page'              => $page,
            'total_pages'       => $totalPages,
            'has_next_page'     => $page < $totalPages,
            'has_previous_page' => $page > 1,
            'prev_page_param'   => $links['prev_link'],
            'this_page_param'   => $links['this_link'],
            'next_page_param'   => $links['next_link'],
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
     * Generate only previous and next pagination links.
     *
     * @param int $currentPage The current page number.
     * @param int $totalPages The total number of pages.
     * @param int $totalPages The base URL for pagination links.
     * @param string $queryParam The query parameter name for pagination.
     * @return array Associative array with URLs.
     */
    public function generatePrevNextLinksParam($currentPage, $perPage, $totalPages, $queryParam = 'page')
    {
        $links = [
            'prev_link' => null,
            'this_link' => null,
            'next_link' => null,
        ];

        // Generate previous page link if applicable
        if ($currentPage > 1) {
            $prevPage = $currentPage - 1;
            $links['prev_link'] = $queryParam. '=' . $prevPage . '&per_page=' . $perPage;
        }

        // Generate this page link if applicable
        if ($currentPage) {
            $links['this_link'] = $queryParam. '=' . $currentPage . '&per_page=' . $perPage;
        }

        // Generate next page link if applicable
        if ($currentPage < $totalPages) {
            $nextPage = $currentPage + 1;
            $links['next_link'] = $queryParam. '=' . $nextPage . '&per_page=' . $perPage;
        }


        return $links;
    }

}
