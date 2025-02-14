<?php

add_action('template_redirect', 'visit_paypal_url');

function visit_paypal_url()
{
    // Check if the URL has the required query variables
    $command = get_query_var('command');
    $order_num = sanitize_text_field(get_query_var('order_num'));
    if ($command === 'paypalrefresh' && $order_num) {
        // Create a new instance of ZIMAPI
        $zim_api = new ZIMAPI(ZIMAPI_BASE);

        try {
            // Call the API to generate the PayPal payment URL
            $response = $zim_api->callAPi("/orders/refresh/" . $order_num);
            error_log("Here is the response" . print_r($response, true));

            // Get the PayPal payment URL from the API response
            if (isset($response["sdata"])) {
                $paypal_url = $response['sdata'];

                // Redirect the user to the PayPal payment URL using the appropriate hook
                wp_redirect($paypal_url);
                exit;
            } else {
                error_log("Key not found!");
            }
        } catch (Exception $e) {
            error_log("Error trying to connect to API" . $e->getMessage());
        }
    }
}

function zp_refresh_paypal_vars($vars)
{
    $vars[] = 'command';
    $vars[] = 'order_num';
    return $vars;
}
add_filter('query_vars', 'zp_refresh_paypal_vars');
