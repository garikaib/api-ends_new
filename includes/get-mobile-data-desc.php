<?php

function zp_get_description(string $key): string
{
    $descriptions = array(
        "data" => "data",
        "wa" => "data",
        "fb" => "data",
        "ig" => "data",
        "tw" => "data",
        "pin" => "data",
        "cross_min" => "voice",
        "offnet_min" => "voice",
        "onnet_min" => "voice",
        "sms" => "sms",
        "night_data" => "data",
        "wa_opeak" => "data",
        "sms_opeak" => "sms",
        "bb" => "data",
    );

    if (!array_key_exists($key, $descriptions)) {
        // throw new Exception("Unknown key: {$key}");
        error_log($key);
        return "unknown";
    } else {
        return $descriptions[$key];}
}
