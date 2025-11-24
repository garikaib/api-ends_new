<?php

namespace ZimPriceCheck\ApiEnds\Admin\CarbonFields;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class CacheSettings
{
    public function __construct()
    {
        $this->register();
    }

    public function register()
    {
        Container::make('theme_options', __('Cache Control'))
            ->set_page_parent('zpc-api') // Parent to the main menu
            ->add_fields($this->get_fields());
    }

    private function get_fields()
    {
        return array(
            Field::make('html', 'crb_cache_settings_header')
                ->set_html('<div class="zpc-header"><img src="' . API_END_URL . 'assets/images/cache_settings.svg" class="zpc-icon" /><div class="zpc-title-group"><h2 class="zpc-title">API Cache Settings</h2><p class="zpc-subtitle">Manage API caching performance.</p></div></div>'),
            
            Field::make('text', 'api_cache_duration', __('Cache Duration (seconds)'))
                ->set_default_value(3600)
                ->set_attribute('type', 'number')
                ->set_help_text('How long to cache API responses in seconds. Default is 3600 (1 hour).')
                ->set_width(50),

            Field::make('html', 'zpc_flush_cache_ui')
                ->set_html('
                    <div class="zpc-flush-cache-wrapper">
                        <button type="button" id="zpc_flush_cache_btn" class="button button-secondary button-large">Flush Cache Now</button>
                        <span id="zpc_flush_cache_status" class="zpc-status-msg"></span>
                        <p class="description">Click to flush the API cache immediately without saving settings.</p>
                    </div>
                ')
                ->set_width(50),
        );
    }
}
