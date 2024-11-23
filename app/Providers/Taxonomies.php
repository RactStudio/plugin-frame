<?php

namespace PluginFrame\Providers;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Taxonomies
{
    public function __construct()
    {
        $this->registerCustomTaxonomies();
    }

    /**
     * Register custom taxonomies with various examples.
     */
    protected function registerCustomTaxonomies()
    {
        $taxonomies = new \PluginFrame\Services\Taxonomies();

        // Example 1: Conditional Taxonomy
        $taxonomies->registerTaxonomy(
            'conditional_taxonomy',
            ['classic_example'], // Post types associated with this taxonomy
            [
                'label' => 'Conditional Taxonomy',
                'public' => true,
                'hierarchical' => true, // Acts like categories
                'show_in_rest' => false, // Disables Gutenberg
            ],
            function () {
                // Example condition: Register only for administrators
                return current_user_can('manage_options');
            }
        );

        // Example 2: Block-Compatible Taxonomy
        $taxonomies->registerTaxonomy(
            'block_taxonomy',
            ['block_example'], // Post types associated with this taxonomy
            [
                'label' => 'Block Taxonomy',
                'public' => true,
                'hierarchical' => false, // Acts like tags
                'show_in_rest' => true, // Enables Gutenberg
            ]
        );
    }
}
