<?php

if (!defined('ABSPATH')) {
    exit;
}

function zp_liquid_home_shortcode($atts)
{
    $liquid_home = new ZP_Liquid_Home();
    return $liquid_home->render($atts);
}
add_shortcode('liquid-home', 'zp_liquid_home_shortcode');
