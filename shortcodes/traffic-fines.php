<?php

/**
 * Shortcode: [traffic-fines]
 */

function zpc_show_traffic_fines_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'type' => null,
        ),
        $atts,
        'traffic-fines'
    );

    // Only run if the shortcode is actually present in the post content
    if (is_singular() && has_shortcode(get_post()->post_content, 'traffic-fines')) {
        $traffic_fines = new ZP_Traffic_Fines();
        return $traffic_fines->render($atts['type']);
    }
    return '';
}
add_shortcode('traffic-fines', 'zpc_show_traffic_fines_shortcode');
