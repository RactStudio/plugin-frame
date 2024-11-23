<?php

namespace PluginFrame\Services;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Taxonomies
{
    /**
     * Register a single taxonomy.
     *
     * @param string $key Taxonomy key/slug.
     * @param array $postTypes Post types to associate this taxonomy with.
     * @param array $args Taxonomy arguments.
     * @param callable|null $condition Optional condition to evaluate before registration.
     */
    public function registerTaxonomy(string $key, array $postTypes, array $args, ?callable $condition = null)
    {
        // Register only if condition is met or no condition is specified
        if ($condition === null || call_user_func($condition)) {
            add_action('init', function () use ($key, $postTypes, $args) {
                register_taxonomy($key, $postTypes, $args);
            });
        }
    }

    /**
     * Register multiple taxonomies at once.
     *
     * @param array $taxonomies Array of taxonomies to register.
     * Format: ['key' => ['post_types' => [], 'args' => [], 'condition' => callable|null]]
     */
    public function registerTaxonomies(array $taxonomies)
    {
        foreach ($taxonomies as $key => $details) {
            $postTypes = $details['post_types'] ?? [];
            $args = $details['args'] ?? [];
            $condition = $details['condition'] ?? null;
            $this->registerTaxonomy($key, $postTypes, $args, $condition);
        }
    }
}
