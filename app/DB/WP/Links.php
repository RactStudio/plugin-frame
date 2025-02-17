<?php

namespace PluginFrame\DB\WP;

use Pluginframe\DB\Utils\QueryBuilder;
use Pluginframe\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Links
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'links';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all links with optional pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function allLinks($page = 1, $perPage = 10)
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

    public function getLink($linkId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('link_id', '=', $linkId)
            ->get();
    }

    public function updateLink($linkId, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('link_id', '=', $linkId)
            ->update($data);
    }

    public function insertLink($data)
    {
        return $this->queryBuilder->table($this->table)->insert($data);
    }

    public function deleteLink($linkId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('link_id', '=', $linkId)
            ->delete();
    }
}
