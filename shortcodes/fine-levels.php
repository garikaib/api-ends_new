<?php

/**
 * Shortcode: [govt-fines] and [fine-levels]
 */

function zpc_show_fine_levels_shortcode($atts)
{
    // Only run if the shortcode is actually present in the post content
    if (is_singular() && (has_shortcode(get_post()->post_content, 'govt-fines') || has_shortcode(get_post()->post_content, 'fine-levels'))) {
        $fine_levels = new ZP_Fine_Levels();
        return $fine_levels->render();
    }
    return '';
}
add_shortcode('govt-fines', 'zpc_show_fine_levels_shortcode');
add_shortcode('fine-levels', 'zpc_show_fine_levels_shortcode');
