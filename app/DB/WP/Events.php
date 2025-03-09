<?php

namespace PluginFrame\DB\WP;

use PluginFrame\DB\Utils\QueryBuilder;
use PluginFrame\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

/**
 * EventsMeta class for interacting with wp_e_events table.
 */
class EventsMeta
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'e_events';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all events with optional pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function allEvents($page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'paginate')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder->table($this->table),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->table($this->table)->get();
    }

    public function getEvent($eventId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('event_id', $eventId)
            ->get();
    }

    public function updateEvent($eventId, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('event_id', $eventId)
            ->update($data);
    }

    public function insertEvent($data)
    {
        return $this->queryBuilder->table($this->table)->insert($data);
    }

    public function deleteEvent($eventId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('event_id', $eventId)
            ->delete();
    }
}
