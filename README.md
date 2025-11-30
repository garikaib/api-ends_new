# API Ends Plugin

A comprehensive WordPress plugin for displaying dynamic prices, exchange rates, and other essential data for Zimbabwe, powered by the ZimPriceCheck API.

## Features

-   **ISP Prices**: Display latest pricing for TelOne, Liquid Home (ZOL), and Utande.
-   **Exchange Rates**: Show real-time USD/ZiG exchange rates, cross rates, and historical data.
-   **Fuel Prices**: Display current and historical fuel prices (Petrol, Diesel, LPG).
-   **Transport Costs**: Show ZUPCO fares, bus fares, tollgate fees, and vehicle licensing costs.
-   **Fines & Fees**: Display government fines (traffic, etc.) and passport fees.
-   **Dynamic Conversions**: Automatically calculate estimated prices in alternative currencies (USD/ZiG).

## Installation

1.  Upload the `api-ends` folder to the `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  Ensure the `ZIMAPI_BASE` constant is defined in your theme or config if not using the default.

## Shortcodes

### ISP Packages

#### TelOne
Display TelOne internet packages.
```
[telone-prices type="All"]
```
**Attributes:**
-   `type`: Filter by package type. Options: `All`, `ADSL`, `Fibre`, `LTE`, `VSAT`, `WiFi`, `USD`.
    -   *Note*: `VSAT` packages include LEO/Starlink notes.

#### Liquid Home (ZOL)
Display Liquid Home internet packages.
```
[liquid-home type="All"]
```
**Attributes:**
-   `type`: Filter by package type. Options: `All`, `Fibre`, `LTE`, `VSAT`.

#### Utande
Display Utande internet packages.
```
[utande type="All"]
```
**Attributes:**
-   `type`: Filter by package type.

### Exchange Rates

#### Latest Rates
Display a comprehensive table of the latest exchange rates.
```
[show-latest-rates]
```

#### USD to ZiG Table
Display a detailed table for USD to ZiG conversions, including street and official rates.
```
[usd-zig]
```

#### ZiG to USD Table
Display a detailed table for ZiG to USD conversions.
```
[zig-usd]
```

#### Call to Action Banner
Display a banner linking to historical rate charts.
```
[zimpricecheck_cta]
```

### Fuel Prices

#### Latest Fuel Prices
Display the current prices for Petrol and Diesel.
```
[show-latest-fuel-prices]
```

#### Historical Fuel Prices
Display a table of historical fuel prices.
```
[historical-fuel-prices-table]
```

#### LP Gas Prices
Display the latest LP Gas prices.
```
[show-latest-lpgas-prices]
```

### Transport & Licensing

#### Transport Costs
Display various transport-related costs.
```
[transport type="zupco"]
```
**Attributes:**
-   `type`: The type of cost to display. Options:
    -   `zupco`: ZUPCO fares.
    -   `busfares`: Inter-city bus fares.
    -   `tollgates`: Standard tollgate fees.
    -   `tollgates_prem`: Premium tollgate fees.
    -   `zinara`: Vehicle licensing fees (ZINARA).

### Fines & Government Fees

#### Traffic Fines
Display standard traffic fines.
```
[traffic-fines]
```

#### Government Fines (Levels)
Display government fine levels.
```
[fine-levels]
```
*Alias:* `[govt-fines]`

#### Passport Fees
Display current passport application fees.
```
[passport-fees]
```
