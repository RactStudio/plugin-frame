<?php

namespace PluginFrame\Providers;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class PostTypes
{
    protected $postTypes;

    public function __construct()
    {
        $this->postTypes = new \PluginFrame\Services\PostTypes();
        
        $this->registerCustomPostTypes();
    }

    /**
     * Register custom post types with various examples.
     */
    protected function registerCustomPostTypes()
    {
        // Example 1: Classic Editor Compatible
        $this->postTypes->registerPostType(
            'classic_example',
            [
                'label' => 'Classic Editor Post',
                'public' => true,
                'show_in_menu' => true, // Set to false to hide from the admin menu
                'supports' => ['title', 'editor', 'thumbnail'],
                'show_in_rest' => false, // Disables Gutenberg (forces classic editor)
            ],
            function () {
                // Example condition: Only register if the user is logged in
                return is_user_logged_in();
            }
        );

        // Example 2: Block Editor Compatible
        $this->postTypes->registerPostType(
            'block_example',
            [
                'label' => 'Block Editor Post',
                'public' => true,
                'show_in_menu' => true, // Set to false to hide from the admin menu
                'supports' => ['title', 'editor', 'thumbnail'],
                'show_in_rest' => true, // Enables Gutenberg
            ]
        );
    }
}
