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

For detailed usage, attributes, and examples, please refer to the documentation files linked below.

### Exchange Rates & Money
*   [Latest Rates](docs/exchange-rates.md) - `[zpc-latest-rates]`
*   [ZiG to USD Converter](docs/zig-usd-converter.md) - `[zig-usd]`
*   [USD to ZiG Converter](docs/usd-zig-converter.md) - `[usd-zig]`
*   [ZiG Withdrawal Limits](docs/zig-limits.md) - `[zig-limits]`

### Internet Service Providers (ISPs) & Data
*   [Liquid Home (ZOL)](docs/liquid-home.md) - `[liquid-home]`
*   [TelOne](docs/telone.md) - `[telone]`
*   [Utande](docs/utande.md) - `[utande]`
*   [Econet Bundles](docs/econet-bundles.md) - `[econet-bundles]`
*   [NetOne Bundles](docs/netone-bundles.md) - `[netone-bundles]`
*   [Telecel Bundles](docs/telecel-bundles.md) - `[telecel-bundles]`

### Fuel & Energy
*   [Fuel Prices](docs/fuel-prices.md) - `[zpc-latest-fuel]`
*   [LP Gas Prices](docs/lpg-prices.md) - `[show-latest-lpgas-prices]`
*   [ZESA Tariffs](docs/zesa-tariffs.md) - `[zesa-tariffs]`

### Transport
*   [Transport (Master Shortcode)](docs/transport.md) - `[transport]`
*   [Tollgates](docs/tollgates.md) - `[tollgates]`
*   [ZUPCO Fares](docs/zupco.md) - `[zupco]`
*   [Bus Fares](docs/bus-fares.md) - `[bus-fares]`
*   [ZINARA Licensing](docs/zinara-license.md) - `[zinara-license]`

### Government & Fines
*   [Passport Fees](docs/passport-fees.md) - `[passport-fees]`
*   [Fine Levels](docs/fine-levels.md) - `[fine-levels]`
*   [Traffic Fines](docs/traffic-fines.md) - `[traffic-fines]`
*   [Births & Deaths](docs/births-deaths.md) - `[births-deaths]`
*   [Citizen Status](docs/citizen-status.md) - `[citizen-status]`

### Consumer Goods & Misc
*   [Groceries](docs/groceries.md) - `[groceries]`
*   [Drink Prices](docs/drink-prices.md) - `[drink-prices]`
*   [WhatsApp Channel](docs/whatsapp-channel.md) - `[zpc_wa_channel_banner]`
*   [Ad Injection](docs/ad-injection.md) - `[before_actual_content]`
