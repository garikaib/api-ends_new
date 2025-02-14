<?php
// Path to the base directory of the plugin

//Transport
function zp_trans_costs($attr)
{
    try {
        $type = "zupco"; //Default type is all
        if (is_array($attr) && array_key_exists("type", $attr)) {
            $type = $attr["type"];
        }

        $transport = new ZIMAPI(ZIMAPI_BASE);
        $latest_prices = "";
        $endPoint = "/rates/fx-rates";
        $latest_rates = $transport->callApi($endPoint, zp_get_remote_ip());
        if ($type === "zupco") {
            $endPoint = "/prices/fares/zupco";
            $latest_prices = $transport->callApi($endPoint, zp_get_remote_ip());
            require_once API_END_BASE . 'templates/zupco.php';
            return buildFareTable($latest_prices, $latest_rates);
        } elseif ($type === "busfares") {
            $endPoint = "/prices/fares/busfares";
            $latest_prices = $transport->callApi($endPoint, zp_get_remote_ip());
            require_once API_END_BASE . 'templates/bus-fares.php';
            return build_b_fare_table($latest_prices, $latest_rates);
        } elseif ($type === "tollgates") {
            $endPoint = "/prices/zinara/tollgates";
            $latest_prices = $transport->callApi($endPoint, zp_get_remote_ip());
            require_once API_END_BASE . 'templates/tollgates.php';
            return buildTGTable($latest_prices, $latest_rates);
        } elseif ($type === "tollgates_prem") {
            $endPoint = "/prices/zinara/tollgates";
            $latest_prices = $transport->callApi($endPoint, zp_get_remote_ip());
            require_once API_END_BASE . 'templates/tollgates-prem.php';
            return buildPTGTable($latest_prices, $latest_rates);
        } elseif ($type === "zinara") {
            $endPoint = "/prices/zinara/fees";
            $wanted = "";
            if ($wanted === "zinara_usd") {
                $wanted = "usd_fees";
            } else {
                $wanted = "zig_fees";
            }

            $latest_prices = $transport->callApi($endPoint, zp_get_remote_ip());
            require_once API_END_BASE . 'templates/zinara-fees.php';
            return buildZINARATable($latest_prices, $latest_rates, $wanted);
        }
    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving ZINARA fees: ' . $e->getMessage());
        // Return an error message to the user
        require_once API_END_BASE . 'includes/class-show-notice.php';

        return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest ZINARA fees at the moment. Please try again later.");
    }
}
add_shortcode('transport', 'zp_trans_costs');
