<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('carbon_fields_register_fields', 'zimpricecheck_api_ends_settings_page');
function zimpricecheck_api_ends_settings_page()
{
    //Add CSS
    wp_enqueue_style(
        'zpc_carbon_fields_styles',
        API_END_URL . 'templates/css/carbon_admin.css'
    );

    Container::make('theme_options', __('ZPC API'))
        ->set_page_menu_position(99999)
        ->set_icon('dashicons-admin-generic')
        ->add_tab(__('General Settings'), array(
            Field::make('html', 'crb_general_settings_header')
                ->set_html('<h2 class="mdc-typography--headline4">General API Settings</h2><hr>'),
            Field::make('text', 'api_base_url', __('API Base URL'))
                ->set_default_value('https://a.garikai.xyz/api')
                ->set_help_text('Enter the base URL for the API')
                ->set_width(100),
            Field::make('text', 'api_test_base_url', __('API Test Base URL'))
                ->set_default_value('https://api.garikai.xyz/api')
                ->set_help_text('Enter the test base URL for the API')
                ->set_width(100),
            Field::make('image', 'site_logo', __('Site Logo'))
                ->set_help_text('Upload your site logo')
                ->set_width(50),
            Field::make('text', 'whatsapp_channel_url', __('WhatsApp Channel URL'))
                ->set_default_value('https://whatsapp.com/channel/0029Va7TvgnFSAtC7qL6Vi3x')
                ->set_help_text('Enter the URL for your WhatsApp channel')
                ->set_width(100),
        ))
        ->add_tab(__('Exchange Rates'), array(
            Field::make('html', 'crb_exchange_rate_header')
                ->set_html('<h2 class="mdc-typography--headline4">Exchange Rate Settings</h2><hr>'),
            Field::make('image', 'exchange_rate_featured_image', __('Featured Image'))
                ->set_value_type('id')
                ->set_help_text('Select the image to use as the featured image for exchange rate posts')
                ->set_width(50),
            Field::make('text', 'notification_emails', __('Notification Email Addresses'))
                ->set_help_text('Enter email addresses separated by commas to receive notifications')
                ->set_width(50),
            Field::make('text', 'rates_notes_title', __('Rates Notes Title'))
                ->set_default_value('Please Note:')
                ->set_width(50),
            Field::make('rich_text', 'rates_notes', __('Rates Notes'))
                ->set_help_text('Add any important notes about the exchange rates here.')
                ->set_width(50),
        ))
        ->add_tab(__('Public Holidays'), array(
            Field::make('html', 'crb_public_holidays_header')
                ->set_html('<h2 class="mdc-typography--headline4">Public Holidays</h2><hr>'),
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
        ))
        ->add_tab(__('Cutoff Dates'), array(
            Field::make('html', 'crb_cutoff_dates_header')
                ->set_html('<h2 class="mdc-typography--headline4">Cutoff Dates</h2><hr>'),
            Field::make('date', 'zig_cutoff_date', __('ZiG Cutoff Date'))
                ->set_storage_format('Y-m-d')
                ->set_default_value('2024-04-30')
                ->set_width(50),
            Field::make('date', 'auction_cutoff_date', __('Auction Cutoff Date'))
                ->set_storage_format('Y-m-d')
                ->set_default_value('2024-01-01')
                ->set_width(50),
            Field::make('date', 'omir_ban_date', __('OMIR Ban Date'))
                ->set_storage_format('Y-m-d')
                ->set_default_value('2020-03-15')
                ->set_width(50),
        ))
        ->add_tab(__('WhatsApp Banner'), array(
            Field::make('html', 'crb_whatsapp_banner_header')
                ->set_html('<h2 class="mdc-typography--headline4">WhatsApp Banner Settings</h2><hr>'),
            Field::make('text', 'whatsapp_banner_title', __('Banner Title'))
                ->set_default_value('Stay Ahead of the Game!')
                ->set_width(50),
            Field::make('textarea', 'whatsapp_banner_text', __('Banner Text'))
                ->set_default_value('Get exclusive updates on prices, deals, and rates directly to your WhatsApp! Don\'t miss out on the best offers from Zimpricecheck.com.')
                ->set_width(50),
            Field::make('text', 'whatsapp_banner_button_text', __('Button Text'))
                ->set_default_value('Join Now!')
                ->set_width(50),
        ))
        ->add_tab(__('Contact Information'), array(
            Field::make('html', 'crb_contact_info_header')
                ->set_html('<h2 class="mdc-typography--headline4">Contact Information</h2><hr>'),
            Field::make('text', 'contact_email', __('Email'))
                ->set_width(50),
            Field::make('text', 'contact_phone', __('Phone'))
                ->set_width(50),

        ));
}

function zimpricecheck_api_ends_load_carbon_fields()
{
    require_once API_END_BASE . '/vendor/autoload.php';
    \Carbon_Fields\Carbon_Fields::boot();
}
add_action('after_setup_theme', 'zimpricecheck_api_ends_load_carbon_fields');
