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
    protected $notices;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notices = new Notice();
        $this->registerNotices();
    }

    /**
     * Register notices for different use cases
     */
    protected function registerNotices()
    {
        // Example 1: Basic notice
        $this->notices->addNotice(
            'Welcome to PluginFrame!',
            'success'
        );

        // Example 2: Notice with a custom icon
        $this->notices->addNotice(
            'Custom notice with a FontAwesome icon.',
            'info',
            true,
            '<i class="fa fa-info-circle"></i>'
        );

        // Example 3: Debug notice
        $this->notices->addDebugNotice(
            'This is a debug-only message thta dismiss is false.',
            true
        );

        // Example 4: Role-based notice
        $this->notices->addRoleBasedNotice(
            'Only admins can see this message.',
            'administrator',
            'warning'
        );

        // Example 5: Conditional notice
        $this->notices->addNotice(
            'Only displayed on the "Settings" admin page.',
            'info',
            true,
            null,
            function () {
                return isset($_GET['page']) && $_GET['page'] === 'plugin-settings';
            }
        );

        // Example 6: Notice with image icon
        $this->notices->addNotice(
            'This notice uses a custom image icon.',
            'error',
            true,
            'https://example.com/icons/error-icon.png'
        );

        // Example 6: Notice with image icon
        $this->notices->addNotice(
            'This notice uses a no (false) icon.',
            'error',
            false,
            false,
        );

        // Example 7: Notice for post edit screen only
        $this->notices->addNotice(
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
