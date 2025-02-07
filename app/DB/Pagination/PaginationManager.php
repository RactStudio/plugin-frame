<?php

namespace Pluginframe\DB\Pagination;

use Exception;
use Pluginframe\DB\Utils\QueryBuilder;

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
     * @param object $request The request object.
     * @param int $page The current page number.
     * @param int $perPage The number of items per page.
     * @param string $sortBy The 'asc' or 'desc' for sorting by comment_date.
     * @param string $table The table name for the query.
     * @param array $select The columns to select (default is all `*`).
     * @param array $where The WHERE conditions.
     * @return array The results or the total record count.
     * @throws Exception If the table name is missing.
     */
    public function getPaginatedResults(QueryBuilder $queryBuilder, $request, $page = 1, $perPage = 10, $sortBy = 'desc', $table = '', $select = '*', $where = [])
    {
        if (empty($table)) {
            throw new Exception('Table name must be specified for pagination.');
        }

        // Get pagination parameters from the request
        $page = intval($request->get_param('page')) ?: intval($page); // Default to page 1 (if not specified)
        $perPage = intval($request->get_param('per_page')) ?: intval($perPage); // Default to 10 items per page (if not specified)
        // Validate and sanitize sort_by
        $sortBy = strtolower((string)$request->get_param('sort_by', $sortBy)); // Use method parameter as fallback
        $sortBy = in_array($sortBy, ['asc', 'desc']) ? $sortBy : 'desc'; // Ensure only 'asc' or 'desc'
        
        $startTime = microtime(true); // Start execution timer

        // Set the query table, columns and orderBy
        $queryBuilder->table($table);
        $queryBuilder->orderBy('comment_date', $sortBy);
        $queryBuilder->select($select);

        // Apply WHERE conditions if provided
        $this->applyWhereConditions($queryBuilder, $where);

        // Apply pagination (LIMIT, OFFSET)
        $this->applyPagination($queryBuilder, $page, $perPage);

        // Fetch paginated results
        $results = $this->fetchResultsWithMetadata($queryBuilder, $page, $perPage, $sortBy, $startTime);

        return $results;
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
    private function fetchResultsWithMetadata(QueryBuilder $queryBuilder, $page, $perPage, $sortBy, $startTime)
    {
        $queryStartTime = microtime(true);
        $results = $queryBuilder->get(); // Execute query
        $queryExecutionTime = microtime(true) - $queryStartTime;

        // Get total records
        $totalRecords = $this->getTotalRecords($queryBuilder);
        $totalPages = ceil($totalRecords / $perPage);

        return [
            'data' => $results,
            'meta' => $this->generatePaginationMetadata($page, $perPage, $sortBy, $totalRecords, $totalPages, $queryExecutionTime, $startTime)
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
    private function generatePaginationMetadata($page, $perPage, $sortBy, $totalRecords, $totalPages, $queryTime, $startTime)
    {
        $serverTime = microtime(true) - $startTime;
        $links = $this->generatePrevNextLinksParam($page, $perPage, $sortBy, $totalPages);

        return [
            'page'              => $page,
            'per_page'          => $perPage,
            'sort_by'           => $sortBy,
            'total_pages'       => $totalPages,
            'total_records'     => $totalRecords,
            'has_next_page'     => $page < $totalPages,
            'has_previous_page' => $page > 1,
            'prev_page_param'   => $links['prev_link'],
            'this_page_param'   => $links['this_link'],
            'next_page_param'   => $links['next_link'],
            'db_query_microseconds' => $queryTime,
            'execution_microseconds' => $serverTime,
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
    public function generatePrevNextLinksParam($currentPage, $perPage, $sortBy, $totalPages, $queryParam = 'page')
    {
        $links = [
            'prev_link' => null,
            'this_link' => null,
            'next_link' => null,
        ];

        if ($currentPage > 1) {
            $prevPage = $currentPage - 1;
            $links['prev_link'] = $queryParam . '=' . $prevPage . '&per_page=' . $perPage . '&sort_by=' . $sortBy;
        }

        if ($currentPage) {
            $links['this_link'] = $queryParam . '=' . $currentPage . '&per_page=' . $perPage . '&sort_by=' . $sortBy;
        }

        if ($currentPage < $totalPages) {
            $nextPage = $currentPage + 1;
            $links['next_link'] = $queryParam . '=' . $nextPage . '&per_page=' . $perPage . '&sort_by=' . $sortBy;
        }

        return $links;
    }
}