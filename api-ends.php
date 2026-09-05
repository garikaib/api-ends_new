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
require_once API_END_BASE . 'includes/Admin/CarbonFields/CacheSettings.php';
require_once API_END_BASE . 'includes/Admin/CarbonFields/DateSettings.php';
require_once API_END_BASE . 'includes/Admin/AdminManager.php';

// Initialize Admin Manager
new \Zimpricecheck\ApiEnds\Admin\AdminManager();

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

require_once plugin_dir_path(__FILE__) . 'includes/class-cached-zimapi.php';
require_once plugin_dir_path(__FILE__) . 'includes/format-prices.php';

//Get and show latest Liquid Home Prices
//Get and show latest Liquid Home Prices
require_once plugin_dir_path(__FILE__) . 'includes/isp/class-liquid-home.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/liquid-home.php';
//Get and show latest TelOne Prices
//Get and show latest TelOne Prices
require_once plugin_dir_path(__FILE__) . 'includes/isp/class-telone.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/telone.php';

//Get latest Utande prices and show in tables

require_once plugin_dir_path(__FILE__) . 'shortcodes/utande.php';

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
// ZESA tariffs MIGRATED to zimpricecheck-tools/prices/ + zesa-calculator/ (August 2026).
// Old code archived to wp-content/_deprecated/zesa-2026-08-23/ and removed from here.
// [drink-prices type=deltaa] (Delta-only alcohol prices) MIGRATED to
// zimpricecheck-tools/prices/ as [alcohol-prices category="..."] (September 2026) — the
// new source covers every wholesaler brand in the survey, not just Delta's, and adds a
// retail-price estimate (wholesale case price × 1.3) alongside the wholesale figure.
// Old code archived to wp-content/_deprecated/alcohol-2026-09-04/ and removed from here.

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

/**
 * Get and show Traffic Fines.
 *
 * @return string HTML table of Traffic Fines or error message if unable to retrieve.
 */


// NOTE: [births-deaths], [citizen-status] and [passport-fees] (plus
// [passport-intro] and [sadc-passport-fees], previously registered via the
// templates/passports-explanations.php require below) have moved to the
// zimpricecheck-tools plugin's civil-registry module — see
// wp-content/plugins/zimpricecheck-tools/civil-registry/. That version adds
// WP transient caching (this one hit the upstream API on every single page
// view), keeps the last known-good fees on an upstream failure instead of
// showing an error, and fixes the birth/death/citizen tables all sharing one
// copy-pasted (and for two of the three, wrong) "Birth and Death Certificate
// Fees" caption.
// NOTE: [zig-usd] and [usd-zig] have moved to the zimpricecheck-tools plugin's
// prices module — see wp-content/plugins/zimpricecheck-tools/prices/. That
// version reuses the same cron-warmed `rates` source [show-latest-rates]
// already runs on, instead of this plugin's uncached ZIMAPI client making its
// own upstream call (fx-rates, and for [zig-usd] also oe-rates) on every render.
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

//Clear cache
require plugin_dir_path(__FILE__) . 'includes/purge-cache.php';
//Append date to price updates
require plugin_dir_path(__FILE__) . 'includes/append-date.php';

//To declutter we have moved shortcodes to their associated template files.

require plugin_dir_path(__FILE__) . 'templates/cvr-licence-fees.php';
require plugin_dir_path(__FILE__) . 'shortcodes/past-rates-banner.php';
require plugin_dir_path(__FILE__) . 'after-content/whatsapp-channel.php';
// Ad rendering is now owned entirely by the Zimpricecheck Tools ad manager (see plugins/zimpricecheck-tools/ads/).
