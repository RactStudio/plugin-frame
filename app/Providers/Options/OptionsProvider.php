<?php

namespace PluginFrame\Providers\Options;

use PluginFrame\Core\Services\Options\OptionManager;

class OptionsProvider
{
    /**
     * @var OptionManager
     */
    private OptionManager $options;

    /**
     * Inject the OptionManager via DI.
     */
    public function __construct(OptionManager $options)
    {
        $this->options = $options;
    }

    /**
     * Register your framework options on admin_init (or your chosen hook).
     */
    public function register(): void
    {
        $this->options->register(
            'pf_footer_text',
            'Powered by PluginFrame',
            [
                'storage'    => ['wp'],            // WP options table
                'group'      => 'general',         
                'customizer' => ['section' => 'footer'],
            ]
        );
        $this->options->register(
            'pf_love_text',
            'Loved by PluginFrame',
            [
                'storage'    => ['custom'],            // WP options table
                'group'      => 'general',         
                'customizer' => ['section' => 'footer'],
            ]
        );
    }
}
