<?php

require_once API_END_BASE . 'historical-rates/email-template.php'; // Add this new line to include the email template file

function send_exchange_rate_notification($subject, $message, $post_id = null)
{
    // Get the notification emails from the Carbon Fields option
    $to = carbon_get_theme_option('notification_emails');

    // Validate and sanitize email addresses
    $to_array = array_filter(array_map('sanitize_email', explode(',', $to)));

    // If no valid emails, log error and return
    if (empty($to_array)) {
        error_log('No valid email addresses found for exchange rate notifications.');
        return;
    }

    // Sanitize subject and message
    $subject = sanitize_text_field($subject);
    $message = wp_kses_post($message);

    // Generate email content using the new template
    $email_content = generate_email_template($subject, $message, $post_id);

    // Set headers (removed as it's not needed for wp_mail and was causing errors)
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>',
    );

    // Send emails
    foreach ($to_array as $email) {
        $result = wp_mail($email, $subject, $email_content);
        if (!$result) {
            error_log("Failed to send exchange rate notification to: $email");
        }
    }
}
// Optional: Add a function to verify the nonce if needed
function verify_exchange_rate_notification($nonce)
{
    return wp_verify_nonce($nonce, 'exchange_rate_notification');
}
