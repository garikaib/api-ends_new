<?php

function zim_price_check_cta_shortcode( $atts = array(), $content = '' ) {
	if ( shortcode_exists( 'zpc_exchange_rates_cta' ) ) {
		return do_shortcode( '[zpc_exchange_rates_cta]' );
	}

	return '';
}

add_shortcode( 'zimpricecheck_cta', 'zim_price_check_cta_shortcode' );
