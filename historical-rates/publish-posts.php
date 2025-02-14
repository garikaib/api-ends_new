<?php

require_once API_END_BASE . 'includes/utils/send-email.php';

// Create the custom table for tracking published posts
function create_exchange_rate_tracking_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'exchange_rate_tracking';

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        iso_date date NOT NULL,
        post_id bigint(20) NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY (iso_date)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_setup_theme', 'create_exchange_rate_tracking_table');

// Schedule the cron job to publish posts at 00:05
function schedule_exchange_rate_post_cron()
{
    if (!wp_next_scheduled('publish_exchange_rate_post')) {
        wp_schedule_event(strtotime('today 00:05'), 'daily', 'publish_exchange_rate_post');
    }
}
add_action('wp', 'schedule_exchange_rate_post_cron');

// Check if a date is a public holiday
function is_public_holiday($date)
{
    if (function_exists('carbon_get_theme_option')) {
        $holidays = carbon_get_theme_option('public_holidays');
        foreach ($holidays as $holiday) {
            if ($holiday['holiday_date'] === $date) {
                return true;
            }
        }
    }
    return false;
}

// Validate ISO date format
function is_valid_iso_date($date)
{
    $date_time = DateTime::createFromFormat('Y-m-d', $date);
    return $date_time && $date_time->format('Y-m-d') === $date && $date_time <= new DateTime();
}

// Publish the exchange rate post
function publish_exchange_rate_post($input = null)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'exchange_rate_tracking';
    $publish_dates = [];
    $messages = [];

    // Determine publish dates based on input
    if (is_string($input) && is_valid_iso_date($input)) {
        $publish_dates[] = $input;
    } elseif (is_array($input) && count($input) === 2 &&
        is_valid_iso_date($input[0]) && is_valid_iso_date($input[1])) {
        $start = new DateTime(min($input));
        $end = new DateTime(max($input));
        $period = new DatePeriod($start, new DateInterval('P1D'), $end->modify('+1 day'));

        foreach ($period as $date) {
            $publish_dates[] = $date->format('Y-m-d');
        }
    } else {
        $publish_dates[] = date('Y-m-d', strtotime('-1 day'));
    }

    // Process each publish date
    foreach ($publish_dates as $publish_date) {
        $publish_day = date('N', strtotime($publish_date));

        if ($publish_day >= 1 && $publish_day <= 5 && !is_public_holiday($publish_date)) {
            // Check if the post has already been published
            $last_posts = get_option('last_5_exchange_rate_posts', []);
            if (!in_array($publish_date, $last_posts)) {
                // Check if the date already exists in the tracking table
                $existing_entry = $wpdb->get_var($wpdb->prepare(
                    "SELECT post_id FROM $table_name WHERE iso_date = %s",
                    $publish_date
                ));

                if (!$existing_entry) {
                    // Fetch content for the post
                    $content = fetch_exchange_rate_content($publish_date);

                    // Create the post
                    $post_id = create_exchange_rate_post($publish_date, $content);

                    if ($post_id) {
                        // Insert into tracking table
                        $wpdb->insert($table_name, [
                            'iso_date' => $publish_date,
                            'post_id' => $post_id,
                        ]);

                        // Update last 5 posts
                        array_unshift($last_posts, $publish_date);
                        $last_posts = array_slice($last_posts, 0, 5);
                        update_option('last_5_exchange_rate_posts', $last_posts);

                        $messages[] = "An exchange rate post has been successfully created for $publish_date. Post ID: $post_id";
                    } else {
                        $messages[] = "Failed to create an exchange rate post for $publish_date.";
                    }
                } else {
                    $messages[] = "An exchange rate post for $publish_date already exists. Post ID: $existing_entry.";
                }
            } else {
                $messages[] = "An exchange rate post for $publish_date already exists in recent posts. No new post was created.";
            }
        } else {
            $reason = is_public_holiday($publish_date) ? "public holiday" : "weekend";
            $messages[] = "Skipping exchange rate post for $publish_date ($reason).";
        }
    }

    // Send a summary email for the batch
    if (!empty($messages)) {
        $subject = "Exchange Rate Post Summary";
        $message = implode("\n", $messages);
        send_exchange_rate_notification($subject, $message);
    }
}
add_action('publish_exchange_rate_post', 'publish_exchange_rate_post');

// Fetch content for the post
function fetch_exchange_rate_content($date)
{
    $shortcode = "[show-historical-rate date=\"{$date}\"]";
    return '<!-- wp:shortcode -->' . $shortcode . '<!-- /wp:shortcode -->';
}

// Create the exchange rate post
function create_exchange_rate_post($date, $content = '', $author_id = 1)
{
    $date_obj = new DateTime($date);
    $formatted_date = $date_obj->format('d F Y');
    $title = "Zimbabwe Official and Black Market Exchange Rates: {$formatted_date}";
    // Create the slug using the full ISO date format
    $slug = 'rates-' . $date_obj->format('d-F-Y');

    $post_data = [
        'post_title' => $title,
        'post_name' => $slug,
        'post_status' => 'publish',
        'post_type' => 'exchange-rates',
        // Set the post date using the ISO date format
        'post_date' => $date_obj->format('Y-m-d H:i:s'),
        'post_content' => $content,
        'post_author' => $author_id,
    ];

    $post_id = wp_insert_post($post_data);

    if ($post_id) {
        // Set the custom ISO date using Carbon Fields
        carbon_set_post_meta($post_id, 'iso_date', $date);

        if (function_exists('carbon_get_theme_option')) {
            $attachment_id = carbon_get_theme_option('exchange_rate_featured_image');
            if ($attachment_id) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }
        return $post_id;
    }
    return false;
}

//Manually publish using wp-cli
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('publish_rates', function ($args) {
        $current_year = date('Y');

        // Check for month or month-year format first
        if (count($args) === 1 || count($args) === 2) {
            $month_year = implode(' ', $args); // Combine arguments if two are provided
            $month_year_parts = explode(' ', $month_year);
            $month = strtolower($month_year_parts[0]);
            $year = isset($month_year_parts[1]) ? $month_year_parts[1] : $current_year;

            // Validate month
            $valid_months = [
                'january', 'february', 'march', 'april', 'may', 'june',
                'july', 'august', 'september', 'october', 'november', 'december',
            ];

            if (in_array($month, $valid_months) && is_numeric($year)) {
                $month_number = date('m', strtotime($month . ' 1'));
                $start_date = "$year-$month_number-01";
                $end_date = date('Y-m-t', strtotime($start_date));

                publish_exchange_rate_post([$start_date, $end_date]);
                WP_CLI::success("Published exchange rate posts for $month_year.");
                return; // Exit after successful processing
            }
        }

        // If not a month-year format, check for ISO dates
        if (count($args) === 1 && is_valid_iso_date($args[0])) {
            publish_exchange_rate_post($args[0]);
            WP_CLI::success("Published exchange rate post for date: {$args[0]}");
        } elseif (count($args) === 2 &&
            is_valid_iso_date($args[0]) && is_valid_iso_date($args[1])) {
            publish_exchange_rate_post([$args[0], $args[1]]);
            WP_CLI::success("Published exchange rate posts from {$args[0]} to {$args[1]}");
        } else {
            WP_CLI::error("Invalid input. Please provide a valid date or month.");
        }
    });
}

// Unpublish exchange rate posts
function unpublish_exchange_rate_posts($input = null)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'exchange_rate_tracking';
    $unpublish_dates = [];
    $messages = [];

    // Determine unpublish dates based on input
    if ($input === 'all') {
        // Get all ISO dates from the tracking table
        $unpublish_dates = $wpdb->get_col("SELECT iso_date FROM $table_name");
    } elseif (is_string($input) && is_valid_iso_date($input)) {
        $unpublish_dates[] = $input;
    } elseif (is_array($input) && count($input) === 2 &&
        is_valid_iso_date($input[0]) && is_valid_iso_date($input[1])) {
        $start = new DateTime(min($input));
        $end = new DateTime(max($input));
        $period = new DatePeriod($start, new DateInterval('P1D'), $end->modify('+1 day'));

        foreach ($period as $date) {
            $unpublish_dates[] = $date->format('Y-m-d');
        }
    } else {
        $messages[] = "Invalid input. Please provide a valid date, date range, or 'all'.";
    }

    // Check if there are any posts to unpublish
    if (!empty($unpublish_dates)) {
        $posts_to_process = count($unpublish_dates);
        $messages[] = "Found $posts_to_process exchange rate posts to process.";

        // Unpublish/Delete posts if --do flag is used
        if (defined('WP_CLI') && WP_CLI && isset($_SERVER['argv']) && in_array('--do', $_SERVER['argv'])) {
            // Check if --delete flag is used
            $delete_posts = in_array('--delete', $_SERVER['argv']);

            foreach ($unpublish_dates as $unpublish_date) {
                // Get the post ID from the tracking table
                $post_id = $wpdb->get_var($wpdb->prepare(
                    "SELECT post_id FROM $table_name WHERE iso_date = %s",
                    $unpublish_date
                ));

                if ($post_id) {
                    if ($delete_posts) {
                        // Delete the post and its tracking data
                        wp_delete_post($post_id, true);
                        $wpdb->delete($table_name, ['post_id' => $post_id]);
                        $messages[] = "Deleted exchange rate post for $unpublish_date. Post ID: $post_id";
                    } else {
                        // Update post status to draft
                        wp_update_post([
                            'ID' => $post_id,
                            'post_status' => 'draft',
                        ]);
                        $messages[] = "Unpublished exchange rate post for $unpublish_date. Post ID: $post_id";
                    }
                } else {
                    $messages[] = "No exchange rate post found for $unpublish_date.";
                }
            }
        }
    }

    // Send a summary email for the batch
    if (!empty($messages)) {
        $subject = "Exchange Rate Post Processing Summary";
        $message = implode("\n", $messages);
        send_exchange_rate_notification($subject, $message);
    }
}

// Manually unpublish using wp-cli
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('unpublish_rates', function ($args) {
        if (isset($args[0])) {
            if ($args[0] === 'all') {
                unpublish_exchange_rate_posts('all');
                WP_CLI::success("Processed all exchange rate posts.");
            } elseif (count($args) === 1 && is_valid_iso_date($args[0])) {
                unpublish_exchange_rate_posts($args[0]);
                WP_CLI::success("Processed exchange rate post for date: {$args[0]}");
            } elseif (count($args) === 2 &&
                is_valid_iso_date($args[0]) && is_valid_iso_date($args[1])) {
                unpublish_exchange_rate_posts([$args[0], $args[1]]);
                WP_CLI::success("Processed exchange rate posts from {$args[0]} to {$args[1]}");
            } else {
                WP_CLI::error("Invalid input. Please provide a valid date, date range, or 'all'.");
            }
        } else {
            WP_CLI::error("Please provide a date, date range, or 'all' as an argument.");
        }
    });
}
