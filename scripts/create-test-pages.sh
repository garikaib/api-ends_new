# Run this script from the project root or scripts directory
# Navigate to correct WP root if needed, or assume we are running in context

# 0. cleanup old pages function
delete_existing_pages() {
    echo "Checking for existing 'V2 Shortcode Tests' pages..."
    EXISTING_IDS=$(wp post list --post_type=page --post_title='V2 Shortcode Tests' --field=ID --format=csv)
    
    if [ ! -z "$EXISTING_IDS" ]; then
        echo "Found existing pages with IDs: $EXISTING_IDS. Deleting..."
        # Iterate to handle multiple IDs causing errors in single command
        for id in $(echo $EXISTING_IDS | tr "," " "); do
            wp post delete $id --force
        done
        echo "Old pages deleted."
    else
        echo "No existing pages found."
    fi
}

delete_existing_pages

echo "Creating V2 Shortcode Test Pages..."

# Create parent page
PARENT_ID=$(wp post create --post_type=page --post_title='V2 Shortcode Tests' --post_status=publish --post_content='Testing all V2 migrated shortcodes organized by service. Click on the child pages below to test each service group.' --porcelain)
echo "Created parent page: ID=$PARENT_ID"

# ============================================
# RATES SERVICE
# ============================================
RATES_ID=$(wp post create --post_type=page --post_title='Rates Service Tests' --post_status=publish --post_parent=$PARENT_ID --post_content='<h2>ZiG to USD Converter</h2>
[zig-usd]

<h2>USD to ZiG Converter</h2>
[usd-zig]

<h2>ZiG Withdrawal Limits</h2>
[zig-limits]' --porcelain)
echo "Created Rates Service page: ID=$RATES_ID"

# ============================================
# GOVT FEES SERVICE
# ============================================
GOVT_ID=$(wp post create --post_type=page --post_title='Government Fees Tests' --post_status=publish --post_parent=$PARENT_ID --post_content='<h2>Passport Fees</h2>
[passport-fees]

<h2>Births and Deaths Registration Fees</h2>
[births-deaths]

<h2>Citizen Status Registration Fees</h2>
[citizen-status]' --porcelain)
echo "Created Govt Fees page: ID=$GOVT_ID"

# ============================================
# ZESA SERVICE
# ============================================
ZESA_ID=$(wp post create --post_type=page --post_title='ZESA Tariffs Tests' --post_status=publish --post_parent=$PARENT_ID --post_content='<h2>ZESA Tariffs Table (Default)</h2>
[zesa-tariffs]

<h2>ZESA Tariffs Explanation View</h2>
[zesa-tariffs type="exp"]' --porcelain)
echo "Created ZESA page: ID=$ZESA_ID"

# ============================================
# CONSUMER GOODS SERVICE
# ============================================
CONSUMER_ID=$(wp post create --post_type=page --post_title='Consumer Goods Tests' --post_status=publish --post_parent=$PARENT_ID --post_content='<h2>Delta Alcohol Prices (Default)</h2>
[drink-prices]

<h2>Delta Alcohol Prices (Explicit Type)</h2>
[drink-prices type="deltaa"]' --porcelain)
echo "Created Consumer Goods page: ID=$CONSUMER_ID"

# ============================================
# TRANSPORT SERVICE
# ============================================
TRANSPORT_ID=$(wp post create --post_type=page --post_title='Transport Tests' --post_status=publish --post_parent=$PARENT_ID --post_content='<h2>Tollgates (Standard)</h2>
[tollgates]

<h2>Tollgates (Premium)</h2>
[tollgates type="premium"]

<h2>ZINARA Vehicle License (ZiG)</h2>
[zinara-license]

<h2>ZINARA Vehicle License (USD)</h2>
[zinara-license currency="usd"]

<h2>ZUPCO Fares</h2>
[zupco]

<h2>Intercity Bus Fares</h2>
[bus-fares]

<h2>Transport Delegator - ZUPCO</h2>
[transport type="zupco"]

<h2>Transport Delegator - Bus Fares</h2>
[transport type="busfares"]

<h2>Transport Delegator - Tollgates</h2>
[transport type="tollgates"]

<h2>Transport Delegator - Premium Tollgates</h2>
[transport type="tollgates_prem"]

<h2>Transport Delegator - ZINARA</h2>
[transport type="zinara"]' --porcelain)
echo "Created Transport page: ID=$TRANSPORT_ID"

# ============================================
# TELECOM SERVICE
# ============================================
TELECOM_ID=$(wp post create --post_type=page --post_title='Telecom Tests' --post_status=publish --post_parent=$PARENT_ID --post_content='<h2>NetOne Bundles (All)</h2>
[netone-bundles]

<h2>NetOne Bundles (Data Only)</h2>
[netone-bundles type="data"]

<h2>NetOne Bundles (Voice Only)</h2>
[netone-bundles type="voice"]

<h2>Econet Bundles (All)</h2>
[econet-bundles]

<h2>Econet Bundles (Data Only)</h2>
[econet-bundles type="data"]

<h2>Telecel Bundles (All)</h2>
[telecel-bundles]

<h2>Telecel Bundles (Data Only)</h2>
[telecel-bundles type="data"]' --porcelain)
echo "Created Telecom page: ID=$TELECOM_ID"

# ============================================
# FINES SERVICE
# ============================================
FINES_ID=$(wp post create --post_type=page --post_title='Fines Tests' --post_status=publish --post_parent=$PARENT_ID --post_content='<h2>Fine Levels</h2>
[fine-levels]

<h2>Government Fines (Alias)</h2>
[govt-fines]

<h2>Traffic Fines (All)</h2>
[traffic-fines]' --porcelain)
echo "Created Fines page: ID=$FINES_ID"

# ============================================
# ISP SERVICE
# ============================================
ISP_ID=$(wp post create --post_type=page --post_title='ISP Tests' --post_status=publish --post_parent=$PARENT_ID --post_content='<h2>Liquid Home Packages</h2>
[liquid-home]

<h2>TelOne Packages</h2>
[telone-packages]

<h2>Utande Packages</h2>
[utande-packages]' --porcelain)
echo "Created ISP page: ID=$ISP_ID"

# ============================================
# FUEL SERVICE
# ============================================
FUEL_ID=$(wp post create --post_type=page --post_title='Fuel Tests' --post_status=publish --post_parent=$PARENT_ID --post_content='<h2>Latest Fuel Prices</h2>
[show-latest-fuel]

<h2>Latest Rates</h2>
[show-latest-rates]' --porcelain)
echo "Created Fuel page: ID=$FUEL_ID"

# ============================================
# GET ALL LINKS
# ============================================
echo ""
echo "============================================"
echo "TEST PAGES CREATED SUCCESSFULLY!"
echo "============================================"
echo ""
echo "PARENT PAGE:"
wp post get $PARENT_ID --field=url
echo ""
echo "CHILD PAGES:"
echo "- Rates Service: $(wp post get $RATES_ID --field=url)"
echo "- Govt Fees: $(wp post get $GOVT_ID --field=url)"
echo "- ZESA: $(wp post get $ZESA_ID --field=url)"
echo "- Consumer Goods: $(wp post get $CONSUMER_ID --field=url)"
echo "- Transport: $(wp post get $TRANSPORT_ID --field=url)"
echo "- Telecom: $(wp post get $TELECOM_ID --field=url)"
echo "- Fines: $(wp post get $FINES_ID --field=url)"
echo "- ISP: $(wp post get $ISP_ID --field=url)"
echo "- Fuel: $(wp post get $FUEL_ID --field=url)"
echo ""
echo "============================================"
