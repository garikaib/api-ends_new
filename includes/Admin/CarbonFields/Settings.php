<?php

namespace ZimPriceCheck\ApiEnds\Admin\CarbonFields;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Settings
{
    public function __construct()
    {
        $this->register();
    }

    public function register()
    {
        Container::make('theme_options', __('Zimpricecheck API'))
            ->set_page_file('zpc-api')
            ->set_page_menu_position(99999)
            ->set_icon(API_END_URL . 'assets/images/settings.svg')
            ->add_tab(__('Core Settings'), $this->get_core_settings())
            ->add_tab(__('Rates & Data'), $this->get_rates_data_settings())
            ->add_tab(__('Marketing'), $this->get_marketing_settings())
            ->add_tab(__('Holidays'), $this->get_holidays_settings());
    }

    private function get_core_settings()
    {
        return array(
            Field::make('html', 'crb_core_header')
                ->set_html('<div class="zpc-header"><img src="' . API_END_URL . 'assets/images/general_settings.svg" class="zpc-icon" /><div class="zpc-title-group"><h2 class="zpc-title">Core Configuration</h2><p class="zpc-subtitle">Configure the core API endpoints and site identity.</p></div></div>'),
            
            Field::make('separator', 'crb_sep_api', __('API Endpoints')),
            Field::make('text', 'api_base_url', __('API Base URL'))
                ->set_default_value('https://a.garikai.xyz/api')
                ->set_help_text('Enter the base URL for the API')
                ->set_width(50),
            Field::make('text', 'api_test_base_url', __('API Test Base URL'))
                ->set_default_value('https://api.garikai.xyz/api')
                ->set_help_text('Enter the test base URL for the API')
                ->set_width(50),

            Field::make('separator', 'crb_sep_identity', __('Identity & Contact')),
            Field::make('image', 'site_logo', __('Site Logo'))
                ->set_help_text('Upload your site logo')
                ->set_width(100),
            Field::make('text', 'contact_email', __('Email'))
                ->set_width(50),
            Field::make('text', 'contact_phone', __('Phone'))
                ->set_width(50),
        );
    }

    private function get_rates_data_settings()
    {
        return array(
            Field::make('html', 'crb_rates_header')
                ->set_html('<div class="zpc-header"><h2 class="zpc-title">Exchange Rates & Data Logic</h2><p class="zpc-subtitle">Manage exchange rate display and critical dates.</p></div>'),

            Field::make('separator', 'crb_sep_display', __('Display Options')),
            Field::make('image', 'exchange_rate_featured_image', __('Featured Image'))
                ->set_value_type('id')
                ->set_help_text('Select the image to use as the featured image for exchange rate posts')
                ->set_width(100),
            Field::make('text', 'rates_notes_title', __('Rates Notes Title'))
                ->set_default_value('Please Note:')
                ->set_width(100),
            Field::make('rich_text', 'rates_notes', __('Rates Notes'))
                ->set_help_text('Add any important notes about the exchange rates here.')
                ->set_width(100),

            Field::make('separator', 'crb_sep_dates', __('Critical Dates')),
            Field::make('date', 'zig_cutoff_date', __('ZiG Cutoff Date'))
                ->set_storage_format('Y-m-d')
                ->set_default_value('2024-04-30')
                ->set_width(33),
            Field::make('date', 'auction_cutoff_date', __('Auction Cutoff Date'))
                ->set_storage_format('Y-m-d')
                ->set_default_value('2024-01-01')
                ->set_width(33),
            Field::make('date', 'omir_ban_date', __('OMIR Ban Date'))
                ->set_storage_format('Y-m-d')
                ->set_default_value('2020-03-15')
                ->set_width(33),

            Field::make('separator', 'crb_sep_notifications', __('Notifications')),
            Field::make('text', 'notification_emails', __('Notification Email Addresses'))
                ->set_help_text('Enter email addresses separated by commas to receive notifications')
                ->set_width(100),
        );
    }

    private function get_marketing_settings()
    {
        return array(
            Field::make('html', 'crb_marketing_header')
                ->set_html('<div class="zpc-header"><h2 class="zpc-title">Marketing & Social</h2><p class="zpc-subtitle">Manage WhatsApp integration and banners.</p></div>'),

            Field::make('separator', 'crb_sep_whatsapp', __('WhatsApp Integration')),
            Field::make('text', 'whatsapp_channel_url', __('WhatsApp Channel URL'))
                ->set_default_value('https://whatsapp.com/channel/0029Va7TvgnFSAtC7qL6Vi3x')
                ->set_help_text('Enter the URL for your WhatsApp channel')
                ->set_width(100),

            Field::make('separator', 'crb_sep_banner', __('Banner Configuration')),
            Field::make('text', 'whatsapp_banner_title', __('Banner Title'))
                ->set_default_value('Stay Ahead of the Game!')
                ->set_width(100),
            Field::make('textarea', 'whatsapp_banner_text', __('Banner Text'))
                ->set_default_value('Get exclusive updates on prices, deals, and rates directly to your WhatsApp! Don\'t miss out on the best offers from Zimpricecheck.com.')
                ->set_width(100),
            Field::make('text', 'whatsapp_banner_button_text', __('Button Text'))
                ->set_default_value('Join Now!')
                ->set_width(100),
        );
    }

    private function get_holidays_settings()
    {
        return array(
            Field::make('html', 'crb_holidays_header')
                ->set_html('<div class="zpc-header"><h2 class="zpc-title">Public Holidays</h2><p class="zpc-subtitle">Define public holidays for the system.</p></div>'),
            
            Field::make('complex', 'public_holidays', __('Holiday Dates'))
                ->add_fields(array(
                    Field::make('date', 'holiday_date', __('Holiday Date'))
                        ->set_storage_format('Y-m-d')
                        ->set_width(50),
                    Field::make('text', 'holiday_name', __('Holiday Name'))
                        ->set_width(50),
                ))
                ->set_layout('tabbed-horizontal')
                ->set_help_text('Add the dates and names of public holidays'),
        );
    }
}
