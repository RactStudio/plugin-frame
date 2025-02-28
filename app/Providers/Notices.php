<?php

namespace PluginFrame\Providers;

use PluginFrame\Services\Notice;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Notices
{
    /**
     * Notices instance
     *
     * @var Notice
     */
    protected $notice;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notice = new Notice();
        // Display Notices (Comment to DISABLE all Notices)
        $this->registerNotices();
    }

    /**
     * Register notices for different use cases
     */
    protected function registerNotices()
    {
        // Example 1: Basic notice
        $this->notice->addNotice(
            'Welcome to PluginFrame!',
            'success'
        );

        // Example 2: Debug notice
        $this->notice->addDebugNotice(
            'This is a debug-only message.',
            true
        );

        // Example 3: Role-based notice
        $this->notice->addRoleBasedNotice(
            'Only admins can see this message.',
            'administrator',
            'warning'
        );

        // Example 4: Conditional notice
        $this->notice->addNotice(
            'Only displayed on the "Settings" admin page without icon or img.',
            'info',
            true,
            false,
            function () {
                return isset($_GET['page']) && $_GET['page'] === 'plugin-frame';
            }
        );

        // Example 5: Notice with image icon
        $this->notice->addNotice(
            'This notice uses a custom image icon.',
            'error',
            true,
            PLUGIN_FRAME_URL.'resources/assets/img/user.png'
        );

        // Example 6: Notice for post edit screen only
        $this->notice->addNotice(
            'Only visible on post editing screens.',
            'info',
            true,
            null,
            function () {
                return function_exists('get_current_screen') && get_current_screen()->base === 'post';
            }
        );
    }
}
