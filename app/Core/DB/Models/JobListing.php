<?php

namespace PluginFrame\Core\DB\Models;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * JobListing Model for interacting with job listings in the database.
 */
class JobListing extends BaseModel
{
    protected $table = 'job_listings';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Paginate job listings with optional category filter.
     *
     * @param int $perPage
     * @param int $currentPage
     * @param int|null $categoryId
     * @return array
     */
    public function paginateListings($perPage = 10, $currentPage = 1, $categoryId = null)
    {
        $offset = ($currentPage - 1) * $perPage;

        if ($categoryId !== null) {
            $this->queryBuilder->where('category_id', '=', $categoryId);
        }

        $results = $this->queryBuilder
            ->limit($perPage)
            ->offset($offset)
            ->get();

        $total = $this->queryBuilder->count();

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
     * Fetch job listings with a custom WHERE clause and JOIN support.
     *
     * @param array $conditions
     * @param array $joins
     * @return array
     */
    public function getListingsWithConditions(array $conditions, array $joins = [])
    {
        foreach ($joins as $join) {
            [$table, $first, $operator, $second, $type] = $join;
            $this->queryBuilder->join($table, $first, $operator, $second, $type);
        }

        return $this->where($conditions);
    }
}
