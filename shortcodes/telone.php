<?php

if (!defined('ABSPATH')) {
    exit;
}

function zp_telone_shortcode($atts)
{
    $telone = new ZP_TelOne();
    return $telone->render((array) $atts);
}
add_shortcode('telone', 'zp_telone_shortcode');
