<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class API_End_Admin_Notices
{
    public function __construct()
    {
        add_action('admin_notices', array($this, 'api_base_url_notice'));
        add_action('admin_notices', array($this, 'featured_image_notice'));
        add_action('admin_notices', array($this, 'notification_emails_notice'));
        add_action('admin_notices', array($this, 'contact_info_notice'));
    }

    public function api_base_url_notice()
    {
        if (!$this->is_api_base_url_set()) {
            $this->display_notice(
                'error',
                __('API End plugin: The API Base URL is not set. Please configure it in the settings.', 'api-end'),
                'api_base_url'
            );
        }
    }

    public function featured_image_notice()
    {
        if (!$this->is_featured_image_set()) {
            $this->display_notice(
                'error',
                __('API End plugin: The Exchange Rate Featured Image is not set. Please configure it in the settings.', 'api-end'),
                'exchange_rate_featured_image'
            );
        }
    }

    public function notification_emails_notice()
    {
        if (!$this->is_notification_email_set()) {
            $this->display_notice(
                'warning',
                __('API End plugin: No notification email addresses are set. Please add at least one email address in the settings to receive notifications.', 'api-end'),
                'notification_emails'
            );
        }
    }

    public function contact_info_notice()
    {
        if (!$this->is_contact_info_set()) {
            $this->display_notice(
                'warning',
                __('API End plugin: Contact information is not fully set. Please add both email and phone number in the Contact Information settings.', 'api-end'),
                'contact_email'
            );
        }
    }

    private function display_notice($type, $message, $field)
    {
        $class = 'notice notice-' . $type;
        $settings_url = admin_url('admin.php?page=crb_carbon_fields_container_zpc_api.php');
        $settings_url .= '#' . $field; // Append the field name to the URL

        printf('<div class="%1$s"><p>%2$s <a href="%3$s">%4$s</a></p></div>',
            esc_attr($class),
            esc_html($message),
            esc_url($settings_url),
            esc_html__('Go to settings', 'api-end')
        );
    }

    private function is_api_base_url_set()
    {
        $api_base_url = carbon_get_theme_option('api_base_url');
        return !empty($api_base_url);
    }

    private function is_featured_image_set()
    {
        $featured_image_id = carbon_get_theme_option('exchange_rate_featured_image');
        return !empty($featured_image_id);
    }

    private function is_notification_email_set()
    {
        $notification_emails = carbon_get_theme_option('notification_emails');
        $email_array = array_filter(array_map('trim', explode(',', $notification_emails)));
        return !empty($email_array);
    }

    private function is_contact_info_set()
    {
        $contact_email = carbon_get_theme_option('contact_email');
        $contact_phone = carbon_get_theme_option('contact_phone');
        return !empty($contact_email) && !empty($contact_phone);
    }
}

new API_End_Admin_Notices();
