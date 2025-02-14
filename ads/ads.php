<?php
// ads.php

// Include the Carbon Fields settings
require_once plugin_dir_path( __FILE__ ) . 'control.php';
/**
 * Global variable to track if we're inside the actual content
 */
global $inside_actual_content;
$inside_actual_content = false;

/**
 * Checks if we're inside the actual content section
 *
 * @since 1.0.0
 *
 * @return boolean True if inside actual content, false otherwise
 */
function is_inside_actual_content() {
   
    global $inside_actual_content;
    return !empty($inside_actual_content);
}

/**
 * Shortcode to mark the beginning of actual content
 *
 * @since 1.0.0
 *
 * @return string Empty string
 */
function before_actual_content_shortcode() {
    global $inside_actual_content;
    $inside_actual_content = true;
    return '';
}

/**
 * Shortcode to mark the end of actual content
 *
 * @since 1.0.0
 *
 * @return string Empty string
 */
function after_actual_content_shortcode() {
    
    global $inside_actual_content;
   
    $inside_actual_content = false;
    return '';
}

/**
 * Checks if ads can be displayed based on post type and homepage.
 *
 * @since 1.0.0
 *
 * @return bool True if ads can be displayed, false otherwise.
 */
function zpc_can_show_ads() {
    $allowed_post_types = ['post', 'page', 'track', 'price_updates', 'exchange-rates'];

    // Don't show on the homepage
    if (is_front_page()) {
        return false;
    }

    // Get the current post object
    $current_post = get_post();

    // Check if the post slug is 'zesa' or the post ID is 11702
    if ($current_post->post_name === 'zesa' || $current_post->ID === 11702) {
        return false;
    }

    // Check if the current post type is allowed
    $current_post_type = get_post_type();
    return in_array($current_post_type, $allowed_post_types, true);
}

/**
 * Displays advertisement banners on specified pages.
 *
 * Handles the rendering of advertisement banners based on
 * predefined conditions and settings. This function can be used
 * both as a shortcode and a content filter.
 *
 * @since 1.0.0
 *
 * @param array $atts {
 *     Optional. An array of attributes for the advertisement display.
 *
 *     @type string $position Position of the ad. Default 'top'.
 *     @type string $size     Size of the ad unit. Default 'medium'.
 * }
 * @param string $content Optional. Content between shortcode tags.
 * @return string HTML markup for the advertisement, or empty string if conditions aren't met.
 */
function zpc_ads_show($content = null) {
    // Ensure we're not in admin or feed context
    if (is_admin() || is_feed()) {
        return $content;
    }
    
    // Check if ads can be shown
    if ( ! zpc_can_show_ads() ) {
        //  error_log('Cant Show Ads!');
        return $content;
    }
    
    $before_ad = adrotate_ad(2);
    
    $after_ad = adrotate_ad(2);

   
    // Only proceed if we are inside the actual content section
    if (is_inside_actual_content()) {
        // Convert content into paragraphs
        $paragraphs = explode( "</p>", $content );
        $paragraph_count = count($paragraphs);
    
    
      if ( $paragraph_count > 2 ) {
             $ad_after_second_paragraph = adrotate_ad(2);
             // Add the ad after the second paragraph
           
            $new_content = implode( "</p>", array_slice( $paragraphs, 0, 2 ) ) . "</p>" . $ad_after_second_paragraph;
            $remaining_paragraphs = array_slice( $paragraphs, 2 );
    
            $remaining_count = count($remaining_paragraphs);
            
            $ad_count = 0;
            
           
           for( $i = 0; $i < $remaining_count; $i++ ) {
            
                $new_content .= $remaining_paragraphs[$i] . "</p>";
                  
                $ad_count++;
                if ( $ad_count == 3 && ($remaining_count - $i) > 3 ) {
                    $ad_after_every_third = adrotate_ad(2);
                    $new_content .= $ad_after_every_third;
                    $ad_count = 0;
                }

            }
            $content = $before_ad . $new_content . $after_ad;
           //   error_log('Inside with Ads after every 3 paragraphs');
    
        }
    
        else {
           $content = $before_ad . $content . $after_ad;
         //  error_log('Inside! but not enough paragraphs!');
           
        }
    }
    
    else {
       // error_log('Not inside!');
    }
    // Return the filtered output
    return $content;
}

add_filter('the_content','zpc_ads_show');
// Register shortcodes
add_shortcode('before_actual_content', 'before_actual_content_shortcode');
add_shortcode('after_actual_content', 'after_actual_content_shortcode');