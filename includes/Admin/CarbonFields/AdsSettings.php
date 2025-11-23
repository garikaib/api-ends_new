<?php

namespace ZimPriceCheck\ApiEnds\Admin\CarbonFields;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class AdsSettings
{
    public function __construct()
    {
        $this->register();
    }

    public function register()
    {
        Container::make('theme_options', __('Zimpricecheck Ads Settings', 'zimpricecheck-ads'))
            ->set_page_menu_title('Zimpricecheck Ads')
            ->set_page_menu_position(30)
            ->add_fields($this->get_fields());
    }

    private function get_fields()
    {
        return array(
            Field::make('html', 'crb_ads_settings_header')
                ->set_html('<div class="zpc-header"><h2 class="zpc-title">Ads Settings</h2><p class="zpc-subtitle">Manage ad placements and visibility.</p></div>'),
            Field::make('text', 'zpc_ads_position', __('Ad Position', 'zimpricecheck-ads'))
                ->set_default_value('top')
                ->set_help_text('Position of the ad (e.g., top, bottom).'),
            Field::make('text', 'zpc_ads_size', __('Ad Size', 'zimpricecheck-ads'))
                ->set_default_value('medium')
                ->set_help_text('Size of the ad unit (e.g., medium, large).'),
            Field::make('checkbox', 'zpc_ads_show_on_homepage', __('Show Ads on Homepage', 'zimpricecheck-ads'))
                ->set_option_value('yes')
                ->set_default_value(false)
                ->set_help_text('Enable this to show ads on the homepage.'),
            Field::make('multiselect', 'zpc_ads_allowed_post_types', __('Allowed Post Types', 'zimpricecheck-ads'))
                ->set_default_value(array('post', 'page', 'track', 'price_updates', 'exchange-rates'))
                ->add_options(array(
                    'post' => 'Post',
                    'page' => 'Page',
                    'track' => 'Track',
                    'price_updates' => 'Price Updates',
                    'exchange-rates' => 'Exchange Rates',
                ))
                ->set_help_text('Select the post types where ads should be displayed.'),
        );
    }
}
