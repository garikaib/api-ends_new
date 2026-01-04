# Transport Prices

## `[transport]`

A master shortcode to display various transport-related prices.

### Usage
```markdown
[transport type="zupco"]
```

### Attributes

*   **type** (required): The type of transport info to display.
    *   `zupco`: ZUPCO fares.
    *   `busfares`: Inter-city bus fares.
    *   `tollgates`: Standard tollgate fees.
    *   `tollgates_prem`: Premium tollgate fees.
    *   `zinara`: Zinara vehicle licensing fees.

### Related Shortcodes
You can also use these specific shortcodes:
*   `[zupco]`
*   `[bus-fares]`
*   `[tollgates]`
*   `[zinara-license]`
