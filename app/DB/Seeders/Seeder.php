<?php

namespace PluginFrame\DB\Seeders;

use PluginFrame\DB\Utils\QueryBuilder;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

/**
 * Seeder class for initializing database with demo data.
 */
class Seeder
{
    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * Run all seeders for the application.
     */
    public function run()
    {
        $this->seedJobCategories();
        $this->seedJobListings();
    }

    /**
     * Seed job categories.
     */
    public function seedJobCategories()
    {
        $categories = [
            ['name' => 'Engineering'],
            ['name' => 'Marketing'],
            ['name' => 'Design'],
        ];

        foreach ($categories as $category) {
            $this->createJobCategory($category);
        }
    }

    /**
     * Insert a job category using QueryBuilder.
     *
     * @param array $category
     */
    private function createJobCategory(array $category)
    {
        // Use QueryBuilder to insert data into the job_categories table
        $this->queryBuilder->table('job_categories')->insert($category);
    }

    /**
     * Seed job listings.
     */
    public function seedJobListings()
    {
        $listings = [
            ['title' => 'Frontend Developer', 'category_id' => 1],
            ['title' => 'Marketing Specialist', 'category_id' => 2],
        ];

        foreach ($listings as $listing) {
            $this->createJobListing($listing);
        }
    }

    /**
     * Insert a job listing using QueryBuilder.
     *
     * @param array $listing
     */
    private function createJobListing(array $listing)
    {
        // Use QueryBuilder to insert data into the job_listings table
        $this->queryBuilder->table('job_listings')->insert($listing);
    }
}
