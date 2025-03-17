<?php 

namespace PluginFrame\Core\Services;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Class ActionsLinks
 * Handles the addition of custom action and description links for the plugin on the plugins page.
 */
class ActionsLinks
{
    /**
     * Adds custom links to the plugin action row (next to activation/deactivation links).
     *
     * @param array $links Current action links for the plugin.
     * @param string $pluginFile Plugin file path (used to target specific plugins).
     * @param array $conditions Conditions for customizing the link visibility.
     * @return array Modified action links with custom additions.
     */
    public function addActionLinks(array $links, string $pluginFile, array $conditions = []): array
    {
        // Ensure customization applies only to the desired plugin
        if (plugin_basename(PLUGIN_FRAME_FILE) !== $pluginFile) {
            return $links;
        }

        // Add a custom link to the beginning (left) if the condition is met
        if (!empty($conditions['left']) && is_callable($conditions['left'])) {
            $customLeftLink = $conditions['left']();
            if ($customLeftLink) {
                array_unshift($links, $customLeftLink);
            }
        }

        // Add a custom link to the end (right) if the condition is met
        if (!empty($conditions['right']) && is_callable($conditions['right'])) {
            $customRightLink = $conditions['right']();
            if ($customRightLink) {
                $links[] = $customRightLink;
            }
        }

        return $links;
    }

    /**
     * Adds custom links below the plugin description on the plugins page.
     *
     * @param array $links Current description links for the plugin.
     * @param string $pluginFile Plugin file path (used to target specific plugins).
     * @param array $conditions Conditions for customizing the link visibility.
     * @return array Modified description links with custom additions.
     */
    public function addDescriptionLinks(array $links, string $pluginFile, array $conditions = []): array
    {
        // Ensure customization applies only to the desired plugin
        if (PLUGIN_FRAME_BASENAME !== $pluginFile) {
            return $links;
        }

        // Add a custom link to the beginning (left) if the condition is met
        if (!empty($conditions['left']) && is_callable($conditions['left'])) {
            $customLeftLink = $conditions['left']();
            if ($customLeftLink) {
                array_unshift($links, $customLeftLink);
            }
        }

        // Add a custom link to the end (right) if the condition is met
        if (!empty($conditions['right']) && is_callable($conditions['right'])) {
            $customRightLink = $conditions['right']();
            if ($customRightLink) {
                $links[] = $customRightLink;
            }
        }

        return $links;
    }
}
