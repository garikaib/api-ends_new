<?php

/**
 * Class ZP_Transport
 *
 * Handles the legacy [transport] shortcode by delegating to specific classes.
 *
 * @package    Api_End
 * @subpackage Api_End/includes/transport
 * @author     Garikai Dzoma <garikaib@gmail.com>
 */
class ZP_Transport
{
    /**
     * Initialize the class and register the shortcode.
     */
    public function __construct()
    {
        add_shortcode('transport', array($this, 'render_shortcode'));
    }

    /**
     * Render the shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'type' => 'zupco', // Default type
            ),
            $atts,
            'transport'
        );

        $type = strtolower($atts['type']);

        // Delegate based on type
        switch ($type) {
            case 'zupco':
                if (class_exists('ZP_Zupco')) {
                    $zupco = new ZP_Zupco();
                    return $zupco->render_shortcode($atts);
                }
                break;

            case 'busfares':
                if (class_exists('ZP_Bus_Fares')) {
                    $bus_fares = new ZP_Bus_Fares();
                    return $bus_fares->render_shortcode($atts);
                }
                break;

            case 'tollgates':
                if (class_exists('ZP_Tollgates')) {
                    $tollgates = new ZP_Tollgates();
                    // Pass type='standard' explicitly or let it default
                    return $tollgates->render_shortcode(['type' => 'standard']);
                }
                break;

            case 'tollgates_prem':
                if (class_exists('ZP_Tollgates')) {
                    $tollgates = new ZP_Tollgates();
                    return $tollgates->render_shortcode(['type' => 'premium']);
                }
                break;

            case 'zinara':
                if (class_exists('ZP_Zinara_License')) {
                    $zinara = new ZP_Zinara_License();
                    // Legacy 'zinara' type usually implied fees. 
                    // The old code checked for 'wanted' param inside the function but the shortcode attr was just 'type'.
                    // Wait, the old code had:
                    // if ($wanted === "zinara_usd") ... else ...
                    // But $wanted was initialized to "" and then checked against itself? 
                    // Line 39: $wanted = "";
                    // Line 40: if ($wanted === "zinara_usd") ...
                    // This means it ALWAYS defaulted to zig_fees in the old code unless I missed something.
                    // Let's look at the old code again.
                    // 39: $wanted = "";
                    // 40: if ($wanted === "zinara_usd") {
                    // It seems the old code had a bug or I missed where $wanted came from. 
                    // Ah, it might have been a variable that was supposed to be passed but wasn't.
                    // In any case, let's default to 'zig' which matches the 'else' block of the old code.
                    return $zinara->render_shortcode(['currency' => 'zig']);
                }
                break;
                
            default:
                // Fallback or error
                return '<p>Invalid transport type specified.</p>';
        }

        return '<p>Transport module not found.</p>';
    }
}
