<?php

namespace PluginFrame\Services;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Notice
{
    /**
     * Add a notice to the WordPress admin area.
     *
     * @param string $message  The notice message.
     * @param string $type     The type of the notice. ('info', 'success', 'warning', 'error')
     * @param bool   $dismiss  Whether the notice is dismissible. Default is true.
     * @param string|bool|null $icon Optional icon HTML, URL, or false to disable.
     * @param callable|null $condition Optional callback for conditional logic.
     */
    public function addNotice($message, $type = 'info', $dismiss = true, $icon = null, $condition = null)
    {
        // Validate the type
        $allowedTypes = ['info', 'success', 'warning', 'error'];
        if (!in_array($type, $allowedTypes))
        {
            $type = 'info';
        }

        // Check condition
        if ($condition && is_callable($condition) && !call_user_func($condition))
        {
            return;
        }

        // Set default icon if $icon is null
        if ($icon === null)
        {
            $icon = $this->getDefaultIcon($type);
        }

        // No icon if $icon is explicitly set to false
        $iconHtml = ($icon === false) ? '' : $this->formatIcon($icon);

        // Notice CSS classes
        $classes = "notice notice-{$type}";
        if ($dismiss)
        {
            $classes .= ' is-dismissible';
        }

        // Hook into admin_notices
        add_action('admin_notices', function () use ($message, $classes, $iconHtml)
        {
            echo sprintf('<div class="%s"><p>%s%s</p></div>', esc_attr($classes), $iconHtml, esc_html($message));
        });
    }

    /**
     * Add a debug notice.
     *
     * @param string $message The debug message.
     * @param bool   $show    Whether to show the debug notice. Default is false.
     * @param string|bool|null $icon Optional icon HTML, URL, or false to disable.
     */
    public function addDebugNotice($message, $show = false, $icon = null)
    {
        if ($show && defined('WP_DEBUG') && WP_DEBUG)
        {
            $this->addNotice($message, 'info', true, $icon, null);
            error_log('Debug Notice: ' . $message);
        }
    }

    /**
     * Add a role-based notice.
     *
     * @param string $message The notice message.
     * @param string $role    The user role to target. Default is 'administrator'.
     * @param string $type    The type of the notice. ('info', 'success', 'warning', 'error')
     * @param bool   $dismiss Whether the notice is dismissible. Default is true.
     * @param string|bool|null $icon Optional icon HTML, URL, or false to disable.
     */
    public function addRoleBasedNotice($message, $role = 'administrator', $type = 'info', $dismiss = true, $icon = null)
    {
        $this->addNotice($message, $type, $dismiss, $icon, function () use ($role)
        {
            return current_user_can($role);
        });
    }

    /**
     * Get default icon based on the notice type.
     *
     * @param string $type The notice type.
     * @return string HTML for the default icon.
     */
    private function getDefaultIcon($type)
    {
        $icons = [
            'info'    => '<span class="dashicons dashicons-info"></span>',
            'success' => '<span class="dashicons dashicons-yes"></span>',
            'warning' => '<span class="dashicons dashicons-warning"></span>',
            'error'   => '<span class="dashicons dashicons-dismiss"></span>',
        ];
        return $icons[$type] ?? '';
    }

    /**
     * Format the icon for rendering in the notice.
     *
     * @param string $icon Icon HTML or URL.
     * @return string HTML for the icon.
     */
    private function formatIcon($icon)
    {
        if (filter_var($icon, FILTER_VALIDATE_URL))
        {
            // If the icon is a URL, display it as an image
            return '<img src="' . esc_url($icon) . '" alt="" style="width:20px;height:20px;margin-right:8px;vertical-align:middle;">';
        }
        return '<span class="notice-icon" style="margin-right:8px;vertical-align:middle;">' . $icon . '</span>';
    }
}
