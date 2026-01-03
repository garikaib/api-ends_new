<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://zimpricecheck.com
 * @since             1.0.17
 * @package           Api_End
 *
 * @wordpress-plugin
 * Plugin Name:       Zimpricecheck API
 * Plugin URI:        https://zimpricecheck.com
 * Description:       The core engine for Zimpricecheck.com, handling API integrations, exchange rates, fuel prices, and fines.
 * Version:           1.0.1
 * Author:            Garikai Dzoma
 * Author URI:        https://zimpricecheck.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       api-end
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('API_END_VERSION', '1.0.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-api-end-activator.php
 */
function activate_api_end()
{
    // require_once plugin_dir_path(__FILE__) . 'includes/class-api-end-activator.php';
    // Api_End_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-api-end-deactivator.php
 */
function deactivate_api_end()
{
    // require_once plugin_dir_path(__FILE__) . 'includes/class-api-end-deactivator.php';
    // Api_End_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_api_end');
register_deactivation_hook(__FILE__, 'deactivate_api_end');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
;
define('API_END_BASE', plugin_dir_path(__FILE__));
define('API_END_URL', plugin_dir_url(__FILE__));
// Load Admin Classes
require_once API_END_BASE . 'includes/Admin/CarbonFields/Settings.php';
require_once API_END_BASE . 'includes/Admin/CarbonFields/AdsSettings.php';
require_once API_END_BASE . 'includes/Admin/CarbonFields/CacheSettings.php';
require_once API_END_BASE . 'includes/Admin/CarbonFields/DateSettings.php';
require_once API_END_BASE . 'includes/Admin/AdminManager.php';

// Initialize Admin Manager
new \ZimPriceCheck\ApiEnds\Admin\AdminManager();

// Load Autoloader
require_once API_END_BASE . 'includes/autoload.php';

// Initialize Modern Architecture
$zpc_plugin = new \ZPC\ApiEnds\Plugin(
    API_END_VERSION,
    API_END_BASE,
    API_END_URL
);

require_once API_END_BASE . 'includes/utils/admin-notices.php';
require_once API_END_BASE . 'includes/class-table-footer.php';

//Do this only after we have loaded carbon fields
function api_ends_init()
{
    if (!defined('ZIMAPI_BASE')) {
        $api_base_url = carbon_get_theme_option('api_base_url');
        if (!empty($api_base_url)) {
            define('ZIMAPI_BASE', $api_base_url);
        }
    }
    if (!defined('ZIMAPI_TEST_BASE')) {
        $api_test_base_url = carbon_get_theme_option('api_test_base_url');
        if (!empty($api_test_base_url)) {
            define('ZIMAPI_TEST_BASE', $api_test_base_url);
        }
    }
}
add_action('init', 'api_ends_init');

require_once plugin_dir_path(__FILE__) . 'includes/utils.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

//Get remote IP for logging
require_once plugin_dir_path(__FILE__) . 'includes/get-ip.php';

/**
 * Show latest exchange rates.
 */
/**
 * Show latest exchange rates.
 */
// require_once plugin_dir_path(__FILE__) . 'templates/rates.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cached-zimapi.php';
require_once plugin_dir_path(__FILE__) . 'includes/format-prices.php';
require_once plugin_dir_path(__FILE__) . 'includes/rates/class-exchange-rates.php';
// require_once plugin_dir_path(__FILE__) . 'shortcodes/latest-rates.php'; // Migrated to ShortcodeController

/**
 * Get and show latest fuel prices.
 *
 * @return string HTML table of latest fuel prices or error message if unable to retrieve.
 */
require_once plugin_dir_path(__FILE__) . 'includes/fuel/class-latest-fuel.php';
// require_once plugin_dir_path(__FILE__) . 'shortcodes/latest-fuel.php'; // Migrated to ShortcodeController
require_once plugin_dir_path(__FILE__) . 'includes/fuel/class-historical-fuel.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/historical-fuel.php';

/**
 * Get and show latest LP Gas prices.
 *
 * @return string HTML table of latest LP Gas prices or error message if unable to retrieve.
 */
require_once plugin_dir_path(__FILE__) . 'includes/fuel/class-lp-gas.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/lp-gas.php';
// Mbare Musika
require_once plugin_dir_path(__FILE__) . 'templates/mbare-report.php';

//Get and show latest Liquid Home Prices
//Get and show latest Liquid Home Prices
// require_once plugin_dir_path(__FILE__) . 'includes/isp/class-liquid-home.php'; // Migrated to ShortcodeController
// require_once plugin_dir_path(__FILE__) . 'shortcodes/liquid-home.php';
//Get and show latest TelOne Prices
//Get and show latest TelOne Prices
// require_once plugin_dir_path(__FILE__) . 'includes/isp/class-telone.php'; // Migrated to ShortcodeController
// require_once plugin_dir_path(__FILE__) . 'shortcodes/telone.php';

//Get latest Utande prices and show in tables

// require_once plugin_dir_path(__FILE__) . 'shortcodes/utande.php';

function netone_data_bundles($attr)
{
    try {
        $type = "All"; //Default type is all
        if (is_array($attr) && array_key_exists("type", $attr)) {
            $type = $attr["type"];
        }
        $filter = "none";
        if (is_array($attr) && array_key_exists("filter", $attr)) {
            $filter = $attr["filter"];
        }

        require_once plugin_dir_path(__FILE__) . 'templates/netone.php';
        require_once plugin_dir_path(__FILE__) . 'includes/get-mobile-data-desc.php';

        $netone = new ZIMAPI(ZIMAPI_BASE);
        $endPoint = "/prices/mnos/bundles/netone";
        $latest_prices = $netone->callApi($endPoint, zp_get_remote_ip());
        $endPoint = "/rates/fx-rates";
        $latest_rates = $netone->callApi($endPoint, zp_get_remote_ip());
        return build_netone_prices($latest_prices, $latest_rates, $type, $filter);
    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving NetOne prices: ' . $e->getMessage());
        // Return an error message to the user
        return '<p><strong>Sorry, we could not retrieve the latest NetOne prices at the moment. Please try again later.</strong></p>';
    }
}
add_shortcode('netone-bundles', 'netone_data_bundles');
//Econet data bundles
function econet_data_bundles($attr)
{
    try {
        $type = "All"; //Default type is all
        if (is_array($attr) && array_key_exists("type", $attr)) {
            $type = $attr["type"];
        }
        $filter = "none";
        if (is_array($attr) && array_key_exists("filter", $attr)) {
            $filter = $attr["filter"];
        }

        require_once plugin_dir_path(__FILE__) . 'templates/econet.php';
        require_once plugin_dir_path(__FILE__) . 'includes/get-mobile-data-desc.php';

        $econet = new ZIMAPI(ZIMAPI_BASE);
        $endPoint = "/prices/mnos/bundles/econet";
        $latest_prices = $econet->callApi($endPoint, zp_get_remote_ip());
        $endPoint = "/rates/fx-rates";
        $latest_rates = $econet->callApi($endPoint, zp_get_remote_ip());
        return buildEconetPrices($latest_prices, $latest_rates, $type, $filter);
    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving Econet prices: ' . $e->getMessage());
        // Return an error message to the user
        return '<p><strong>Sorry, we could not retrieve the latest Econet prices at the moment. Please try again later.</strong></p>';
    }
}
add_shortcode('econet-bundles', 'econet_data_bundles');
//Telecel data bundles
//Econet data bundles
function telecel_data_bundles($attr)
{
    try {
        $type = "All"; //Default type is all
        if (is_array($attr) && array_key_exists("type", $attr)) {
            $type = $attr["type"];
        }
        $filter = "none";
        if (is_array($attr) && array_key_exists("filter", $attr)) {
            $filter = $attr["filter"];
        }

        require_once plugin_dir_path(__FILE__) . 'templates/telecel.php';
        require_once plugin_dir_path(__FILE__) . 'includes/get-mobile-data-desc.php';

        $telecel = new ZIMAPI(ZIMAPI_BASE);
        $endPoint = "/prices/mnos/bundles/telecel";
        $latest_prices = $telecel->callApi($endPoint, zp_get_remote_ip());
        $endPoint = "/rates/fx-rates";
        $latest_rates = $telecel->callApi($endPoint, zp_get_remote_ip());
        return buildTelecelPrices($latest_prices, $latest_rates, $type, $filter);

    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving Telecel tariffs: ' . $e->getMessage());
        // Return an error message to the user
        require_once plugin_dir_path(__FILE__) . 'includes/class-show-notice.php';

        return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Telecel Tariffs at the moment. Please try again later.");
    }
}
add_shortcode('telecel-bundles', 'telecel_data_bundles');
//ZESA tariffs
function zesa_tariffs($attr)
{
    $type = "tariffs"; //Default type is all
    if (is_array($attr) && array_key_exists("type", $attr)) {
        $type = $attr["type"];
    }

    $zesa = new ZIMAPI(ZIMAPI_BASE);
    $endPoint = "/prices/zesa";
    $latest_prices = $zesa->callApi($endPoint, zp_get_remote_ip());
    $endPoint = "/rates/fx-rates";
    $latest_rates = $zesa->callApi($endPoint, zp_get_remote_ip());
    if ($type === "exp") {
        require_once plugin_dir_path(__FILE__) . 'templates/zesa-explanation.php';
        return buildZESAExp($latest_prices, $latest_rates);

    } else {
        require_once plugin_dir_path(__FILE__) . 'templates/zesa-tariffs.php';

        return build_zesa_table($latest_prices, $latest_rates);
    }
}
add_shortcode('zesa-tariffs', 'zesa_tariffs');
//Delta Alcohol
function zp_drink_prices($attr)
{
    try {
        $type = "deltaa"; //Default type is all
        if (is_array($attr) && array_key_exists("type", $attr)) {
            $type = $attr["type"];
        }

        $drinks = new ZIMAPI(ZIMAPI_BASE);
        $latest_prices = "";
        $endPoint = "/rates/fx-rates";
        $latest_rates = $drinks->callApi($endPoint, zp_get_remote_ip());
        if ($type === "deltaa") {
            $endPoint = "/prices/drinks/deltaa";
            $latest_prices = $drinks->callApi($endPoint, zp_get_remote_ip());
            require_once plugin_dir_path(__FILE__) . 'templates/delta-alcohol.php';
            return build_delta_a_table($latest_prices, $latest_rates);
        } else {

            require_once plugin_dir_path(__FILE__) . 'templates/zesa-tariffs.php';
            return buildZESATable($latest_prices, $latest_rates);
        }
    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving Delta prices: ' . $e->getMessage());
        // Return an error message to the user
        require_once plugin_dir_path(__FILE__) . 'includes/class-show-notice.php';

        return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Delta prices at the moment. Please try again later.");
    }
}
add_shortcode('drink-prices', 'zp_drink_prices');

//Transport and ZINARA

require_once plugin_dir_path(__FILE__) . 'includes/transport/class-tollgates.php';
new ZP_Tollgates();
require_once plugin_dir_path(__FILE__) . 'includes/transport/class-zinara-license.php';
new ZP_Zinara_License();
require_once plugin_dir_path(__FILE__) . 'includes/transport/class-zupco.php';
new ZP_Zupco();
require_once plugin_dir_path(__FILE__) . 'includes/transport/class-bus-fares.php';
new ZP_Bus_Fares();
require_once plugin_dir_path(__FILE__) . 'includes/transport/class-transport.php';
new ZP_Transport();

//Grocery Prices

require_once plugin_dir_path(__FILE__) . 'templates/groceries.php';
require_once plugin_dir_path(__FILE__) . 'templates/groceries-new.php';

/**
 * Get and show latest Fine Levels.
 *
 * @return string HTML table of latest Fine Levels or error message if unable to retrieve.
 */
require_once plugin_dir_path(__FILE__) . 'includes/fines/class-fine-levels.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/fine-levels.php';

/**
 * Get and show Traffic Fines.
 *
 * @return string HTML table of Traffic Fines or error message if unable to retrieve.
 */
require_once plugin_dir_path(__FILE__) . 'includes/fines/class-traffic-fines.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/traffic-fines.php';


function zp_govt_births($attr)
{
    try {

        $fees = new ZIMAPI(ZIMAPI_BASE);
        $latest_fees = "";
        $endPoint = "/fees/births-deaths";
        $latest_fees = $fees->callApi($endPoint, zp_get_remote_ip());
        $endPoint = "/rates/fx-rates";
        $latest_rates = $fees->callApi($endPoint, zp_get_remote_ip());
        require_once plugin_dir_path(__FILE__) . 'templates/births-deaths.php';
        return build_bd_registration_table($latest_fees, $latest_rates);
    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving Birth/Death Fees: ' . $e->getMessage());
        // Return an error message to the user
        require_once plugin_dir_path(__FILE__) . 'includes/class-show-notice.php';

        return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Birth/Death fees at the moment. Please try again later.");
    }
}
add_shortcode('births-deaths', 'zp_govt_births');

function zp_govt_citizens($attr)
{
    try {

        $fees = new ZIMAPI(ZIMAPI_BASE);
        $latest_fees = "";
        $endPoint = "/fees/citizen-status";
        $latest_fees = $fees->callApi($endPoint, zp_get_remote_ip());
        $endPoint = "/rates/fx-rates";
        $latest_rates = $fees->callApi($endPoint, zp_get_remote_ip());
        require_once plugin_dir_path(__FILE__) . 'templates/citizens-status.php';
        return buildCitRegistrationTable($latest_fees, $latest_rates);
    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving national registration fees: ' . $e->getMessage());
        // Return an error message to the user
        require_once plugin_dir_path(__FILE__) . 'includes/class-show-notice.php';

        return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest National Registration Fees at the moment. Please try again later.");
    }
}
add_shortcode('citizen-status', 'zp_govt_citizens');

function zp_govt_passports($attr)
{
    try {

        $fees = new ZIMAPI(ZIMAPI_BASE);
        $latest_fees = "";
        $endPoint = "/fees/passport";
        $latest_fees = $fees->callApi($endPoint, zp_get_remote_ip());
        $endPoint = "/rates/fx-rates";
        $latest_rates = $fees->callApi($endPoint, zp_get_remote_ip());
        require_once plugin_dir_path(__FILE__) . 'templates/passport-fees.php';
        return buildPassFeesTable($latest_fees, $latest_rates);
    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving Passport Fees: ' . $e->getMessage());
        // Return an error message to the user
        require_once plugin_dir_path(__FILE__) . 'includes/class-show-notice.php';

        return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Passport Fees at the moment. Please try again later.");
    }
}
add_shortcode('passport-fees', 'zp_govt_passports');
require_once plugin_dir_path(__FILE__) . 'includes/rates/class-zig-usd.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/zig-usd.php';
require_once plugin_dir_path(__FILE__) . 'includes/rates/class-usd-zig.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/usd-zig.php';
function zp_zig_usd_withdrawal_limits($attr)
{
    try {
        $rates = new ZIMAPI(ZIMAPI_BASE);
        $endPoint = "/rates/fx-rates";
        $latest_rates = $rates->callApi($endPoint, zp_get_remote_ip());
        require_once plugin_dir_path(__FILE__) . 'templates/zig-withdrawal-limits.php';
        return build_zig_withdrawal_limits_table($latest_rates);
    } catch (Exception $e) {
        // Log the error
        error_log('Error retrieving ZiG Withdrawal limits: ' . $e->getMessage());
        // Return an error message to the user
        require_once plugin_dir_path(__FILE__) . 'includes/class-show-notice.php';

        return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest ZiG wihdrawal limits at the moment. Please try again later.");
    }
}
add_shortcode('zig-limits', 'zp_zig_usd_withdrawal_limits');
// Get and show latest ZBC License Fees
require_once API_END_BASE . 'templates/zbc-licences.php';

//Add Order Received Page Allows us to mark PayPal payment as complete
//Waiting for IPN has been unreliable so far. we will fix this later and may be get rid of this.
require_once plugin_dir_path(__FILE__) . 'templates/order-received.php';
//Test order received for when we are testing

require_once plugin_dir_path(__FILE__) . 'templates/test-order.php';

//Redirect if user is trying to pay for an old order
require_once plugin_dir_path(__FILE__) . 'includes/generate-paypal.php';

function load_schema_files_if_yoast_active()
{
    // Check if Yoast SEO plugin is active
    if (is_plugin_active('wordpress-seo/wp-seo.php')) {
        //Add Schema to various pages
        //Rate Schema
        require_once plugin_dir_path(__FILE__) . 'includes/schema/rates-schema.php';
        //Mbare Prices page
        require_once plugin_dir_path(__FILE__) . 'includes/schema/mbare-schema.php';
        //Fuel Prices page
        require_once plugin_dir_path(__FILE__) . 'includes/schema/fuel-schema.php';
        //LP Gas
        require_once plugin_dir_path(__FILE__) . 'includes/schema/lp-gas-schema.php';
    }
}
add_action('init', 'load_schema_files_if_yoast_active');

//Allow us to add previous day to rates list using REST API every time we update following date's rate
require plugin_dir_path(__FILE__) . 'includes/publish-rates.php';

//Clear cache
require plugin_dir_path(__FILE__) . 'includes/purge-cache.php';
//Append date to price updates
require plugin_dir_path(__FILE__) . 'includes/append-date.php';

//Add hook for custom queries involving orders
require plugin_dir_path(__FILE__) . 'templates/sales/add-tokens.php';

//To declutter we have moved shortcodes to their associated template files.

require plugin_dir_path(__FILE__) . 'templates/cvr-licence-fees.php';
require plugin_dir_path(__FILE__) . 'templates/passports-explanations.php';
require plugin_dir_path(__FILE__) . 'historical-rates/historical-rates.php';
require plugin_dir_path(__FILE__) . 'shortcodes/past-rates-banner.php';
require plugin_dir_path(__FILE__) . 'after-content/whatsapp-channel.php';
require plugin_dir_path(__FILE__) . 'ads/ads.php';

//Table shortcodes



//To DO: Debug Block here
// require plugin_dir_path(__FILE__) . 'blocks/contact-info-block.php';
