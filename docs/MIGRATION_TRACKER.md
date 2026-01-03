# Shortcode Migration Tracker

This document tracks the migration status of all shortcodes in the `api-ends` plugin.

## Status Key
- **Code Migrated**: Shortcode moved to V2 ShortcodeController architecture
- **V2 API**: Service uses V2 API endpoints (e.g., `/v2/rates/fx-rates` instead of `/rates/fx-rates`)
- **Parity Tested**: Frontend output verified to match legacy implementation

---

## Migration Status Table

| Shortcode | Modern Name | Service | Code Migrated | V2 API | Parity Tested |
|-----------|-------------|---------|:-------------:|:------:|:-------------:|
| `[show-latest-rates]` | `[zpc-latest-rates]` | RatesService | ✅ | ⏳ | ⏳ |
| `[show-latest-fuel-prices]` | `[zpc-latest-fuel]` | FuelService | ✅ | ⏳ | ⏳ |
| `[liquid-home]` | `[liquid-home]` | IspService | ✅ | ⏳ | ⏳ |
| `[telone]` | `[telone]` | IspService | ✅ | ⏳ | ⏳ |
| `[utande]` | `[utande]` | IspService | ✅ | ⏳ | ⏳ |
| `[zig-usd]` | `[zig-usd]` | RatesService | ✅ | ✅ | ✅ |
| `[usd-zig]` | `[usd-zig]` | RatesService | ✅ | ✅ | ✅ |
| `[zig-limits]` | `[zig-limits]` | RatesService | ✅ | ✅ | ✅ |
| `[passport-fees]` | `[passport-fees]` | GovtService | ✅ | ⏳ | ⏳ |
| `[births-deaths]` | `[births-deaths]` | GovtService | ✅ | ⏳ | ⏳ |
| `[citizen-status]` | `[citizen-status]` | GovtService | ✅ | ⏳ | ⏳ |
| `[zesa-tariffs]` | `[zesa-tariffs]` | ZesaService | ✅ | ✅ | ✅ |
| `[drink-prices]` | `[drink-prices]` | ConsumerGoodsService | ✅ | ⏳ | ⏳ |
| `[tollgates]` | `[tollgates]` | TransportService | ✅ | ⏳ | ⏳ |
| `[zinara-license]` | `[zinara-license]` | TransportService | ✅ | ⏳ | ⏳ |
| `[zupco]` | `[zupco]` | TransportService | ✅ | ⏳ | ⏳ |
| `[bus-fares]` | `[bus-fares]` | TransportService | ✅ | ⏳ | ⏳ |
| `[transport]` | `[transport]` | TransportService | ✅ | ⏳ | ⏳ |
| `[netone-bundles]` | `[netone-bundles]` | TelecomService | ✅ | ⏳ | ⏳ |
| `[econet-bundles]` | `[econet-bundles]` | TelecomService | ✅ | ⏳ | ⏳ |
| `[telecel-bundles]` | `[telecel-bundles]` | TelecomService | ✅ | ⏳ | ⏳ |
| `[fine-levels]` | `[fine-levels]` | FinesService | ✅ | ⏳ | ⏳ |
| `[govt-fines]` | `[govt-fines]` | FinesService | ✅ | ⏳ | ⏳ |
| `[traffic-fines]` | `[traffic-fines]` | FinesService | ✅ | ⏳ | ⏳ |

---

## Test URLs (Local)

| Page | URL |
|------|-----|
| Rates Service | http://zpc.local/v2-shortcode-tests/rates-service-tests/ |
| Govt Fees | http://zpc.local/v2-shortcode-tests/government-fees-tests/ |
| ZESA | http://zpc.local/v2-shortcode-tests/zesa-tariffs-tests/ |
| Consumer Goods | http://zpc.local/v2-shortcode-tests/consumer-goods-tests/ |
| Transport | http://zpc.local/v2-shortcode-tests/transport-tests/ |
| Telecom | http://zpc.local/v2-shortcode-tests/telecom-tests/ |
| Fines | http://zpc.local/v2-shortcode-tests/fines-tests/ |
| ISP | http://zpc.local/v2-shortcode-tests/isp-tests/ |
| Fuel | http://zpc.local/v2-shortcode-tests/fuel-tests/ |

---

## V2 API Endpoint Reference

| Legacy Endpoint | V2 Endpoint |
|-----------------|-------------|
| `/rates/fx-rates` | `/v2/rates/fx-rates` |
| `/rates/oe-rates/raw` | `/v2/rates/oe-rates/raw` |
| `/prices/fuel` | `/v2/fuel/prices` |
| `/prices/govt/*` | `/v2/fees/*` |
| `/prices/zesa-tariffs` | `/v2/prices/zesa` |
| `/prices/isp/*` | `/v2/prices/isps/*` |
| `/prices/mnos/bundles/*` | `/v2/prices/mobile-bundles/*` |
