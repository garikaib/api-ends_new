<?php

/**
 * Class ZP_Table_Footer
 * 
 * Handles the generation of table footer notes, specifically regarding currency calculations.
 */
class ZP_Table_Footer
{
    /**
     * Render the table footer notes.
     *
     * @param array $args Arguments for the footer.
     *                    - 'calculated_currency' (string): The currency that is being calculated (e.g., 'ZiG', 'USD').
     * @return string HTML content of the footer.
     */
    public static function render(array $args = []): string
    {
        $calculated_currency = $args['calculated_currency'] ?? 'ZiG';
        $bundle_description = $args['bundle_description'] ?? '';

        $html = "<h4>NB</h4>";
        $html .= "<ul>";
        
        $note_text = esc_html($calculated_currency) . " equivalent prices";
        if (!empty($bundle_description)) {
            $note_text .= " for " . esc_html($bundle_description);
        }
        $note_text .= " are automatically updated by our system based on the prevailing market rates.";

        $html .= "<li>" . $note_text . "</li>";
        $html .= "<li>This is done for informational purposes only.</li>";
        $html .= "</ul>";

        return $html;
    }
}
