<?php

namespace PluginFrame\Providers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use PluginFrame\Core\Services\Taxonomies as TaxonomiesService;

class TaxonomiesProvider
{
    protected $taxonomies;

    public function __construct()
    {
        $this->taxonomies = new TaxonomiesService();

        $this->registerCustomTaxonomies();
    }

    /**
     * Register custom taxonomies with various examples.
     */
    protected function registerCustomTaxonomies()
    {
        // Example 1: Conditional Taxonomy
        $this->taxonomies->registerTaxonomy(
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
        $this->taxonomies->registerTaxonomy(
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
