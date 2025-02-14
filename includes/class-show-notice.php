<?php

class ZP_SHOW_NOTICE
{

    /**
     * Show info message
     *
     * @param string $message
     * @return string
     */
    public static function showInfo($message)
    {
        wp_enqueue_style('zp_show_motifications', plugin_dir_url(__FILE__) . 'css/zp_show_notice.css');
        return '<div class="zp_info_message"><strong>Info: </strong>' . esc_html($message) . '</div>';
    }

    /**
     * Show success message
     *
     * @param string $message
     * @return string
     */
    public static function showSuccess($message)
    {
        wp_enqueue_style('zp_show_motifications', plugin_dir_url(__FILE__) . 'css/zp_show_notice.css');
        return '<div class="zp_success_message"><strong>Success: </strong>' . esc_html($message) . '</div>';
    }

    /**
     * Show warning message
     *
     * @param string $message
     * @return string
     */
    public static function showWarning($message)
    {
        wp_enqueue_style('zp_show_motifications', plugin_dir_url(__FILE__) . 'css/zp_show_notice.css');
        return '<div class="zp_warning_message"><strong>Warning: </strong>' . esc_html($message) . '</div>';
    }

    /**
     * Show error message
     *
     * @param string $message
     * @return string
     */
    public static function showError($message)
    {
        wp_enqueue_style('zp_show_motifications', plugin_dir_url(__FILE__) . 'css/zp_show_notice.css');
        return '<div class="zp_error_message"><strong>Error: </strong>' . esc_html($message) . '</div>';
    }
}
