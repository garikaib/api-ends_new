<?php
require_once plugin_dir_path(__DIR__) . "includes/zesa-tariff-calculator.php";

error_log("We are now running ZESA table!");
function zp_zesa_tariff_table(array $api_data, array $rates): string
{
    error_log(calculate_electricity_cost($api_data, 48));
    return "45";
}
