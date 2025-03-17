<?php 

namespace PluginFrame\Providers;

use PluginFrame\Core\Services\ActionsLinks as ActionsLink;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Class ActionsLinks
 * Registers hooks to modify plugin action and description links on the plugins page.
 */
class ActionsLinks
{
    protected $actionLink;

    public function __construct()
    {
        $this->actionLink = new ActionsLink();

        // Register custom action links
        add_filter('plugin_action_links', [$this, 'customizeActionLinks'], 10, 2);

        // Register custom description links
        add_filter('plugin_row_meta', [$this, 'customizeDescriptionLinks'], 10, 2);
    }

    /**
     * Customize action links (activation/deactivation row).
     *
     * @param array $links Current action links.
     * @param string $pluginFile Plugin file path.
     * @return array Modified action links.
     */
    public function customizeActionLinks(array $links, string $pluginFile): array
    {
        // Define conditions for action links
        $conditions = [
            'left' => function (): string {
                return '<a href="' . esc_url(admin_url('admin.php?page=plugin-frame-settings')) . '">Settings</a>';
            },
            'right' => function (): string {
                return '<a href="' . esc_url('https://pluginframe.com/documentation') . '" target="_blank">Docs</a>';
            },
        ];

        return $this->actionLink->addActionLinks($links, $pluginFile, $conditions);
    }

    /**
     * Customize description links (below the plugin description).
     *
     * @param array $links Current description links.
     * @param string $pluginFile Plugin file path.
     * @return array Modified description links.
     */
    public function customizeDescriptionLinks(array $links, string $pluginFile): array
    {
        // Define conditions for description links
        $conditions = [
            'left' => function (): string {
                return '<a href="' . esc_url(admin_url('admin.php?page=plugin-frame-about')) . '">About</a>';
            },
            'right' => function (): string {
                return '<a href="' . esc_url('https://pluginframe.com/support') . '" target="_blank">Support</a>';
            },
        ];

        return $this->actionLink->addDescriptionLinks($links, $pluginFile, $conditions);
    }
}
