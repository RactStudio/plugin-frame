<?php

namespace PluginFrame\DB\Models;

use PluginFrame\DB\Utils\QueryBuilder;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * JobCategory Model for interacting with job categories in the database.
 */
class JobCategory extends BaseModel
{
    protected $table = 'job_categories';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Fetch job categories with their count of listings, with pagination support.
     *
     * @param int $perPage
     * @param int $currentPage
     * @return array
     */
    public function paginateCategoriesWithCount($perPage = 10, $currentPage = 1)
    {
        $offset = ($currentPage - 1) * $perPage;

        $results = $this->queryBuilder
            ->select(['job_categories.*', 'COUNT(job_listings.ID) AS listing_count'])
            ->join('job_listings', 'job_categories.ID', '=', 'job_listings.category_id')
            ->groupBy('job_categories.ID')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        $total = $this->queryBuilder
            ->selectRaw('COUNT(DISTINCT job_categories.ID) AS total_count')
            ->join('job_listings', 'job_categories.ID', '=', 'job_listings.category_id')
            ->count();

        return [
            'data' => $results,
            'pagination' => [
                'total' => $total,
                'perPage' => $perPage,
                'currentPage' => $currentPage,
                'lastPage' => ceil($total / $perPage),
            ],
        ];
    }

    /**
     * Get all categories with a specific condition.
     *
     * @param array $conditions
     * @return array
     */
    public function getCategoriesWithConditions(array $conditions)
    {
        return $this->where($conditions);
    }
}
