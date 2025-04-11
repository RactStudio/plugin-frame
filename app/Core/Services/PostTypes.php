<?php

namespace PluginFrame\Core\Services;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class PostTypes
{
    /**
     * Register a single custom post type.
     *
     * @param string $key Post type key/slug.
     * @param array $args Post type arguments.
     * @param callable|null $condition Optional condition to evaluate before registration.
     */
    public function registerPostType(string $key, array $args, ?callable $condition = null)
    {
        // Register only if condition is met or no condition is specified
        if ($condition === null || call_user_func($condition)) {
            add_action('init', function () use ($key, $args) {
                register_post_type($key, $args);
            });
        }
    }

    /**
     * Register multiple custom post types at once.
     *
     * @param array $postTypes Array of post types to register.
     * Format: ['key' => ['args' => [], 'condition' => callable|null]]
     */
    public function registerPostTypes(array $postTypes)
    {
        foreach ($postTypes as $key => $details) {
            $args = $details['args'] ?? [];
            $condition = $details['condition'] ?? null;
            $this->registerPostType($key, $args, $condition);
        }
    }
}
