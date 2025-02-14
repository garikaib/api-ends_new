<?php
/**
 * Returns the HTML table containing list of ZiG notes and their USD equivalent values.
 *
 * @param array $data The data containing prices and updatedAt information.
 * @return string The HTML table string.
 */
function build_usd_zig_table(array $rates)
{
    // Convert date to DateTime object and set the timezone
    $date = new DateTime('now', new DateTimeZone('Africa/Harare'));
    // Format the date in the desired format
    $formatted_date = $date->format('l, j F Y');

    // ZiG notes with their values
    $zig_notes = [
        1 => 'US$1 Note',
        2 => 'US$2 Note',
        5 => 'US$5 Note',
        10 => 'US$10 Note',
        20 => 'US$20 Note',
        50 => 'US$50 Note',
        100 => 'US$100 Note',
    ];

    // Start building the HTML table
    $table_header = '
    <h4>Convert US dollar notes to Zimbabwe Gold (ZiG) on ' . wp_kses_post($formatted_date) . '</h4>
    <figure class="wp-block-table"> <table>

            <thead>
                <tr>
                <th scope="col">USD Note <img src="https://zimpricecheck.com/wp-content/uploads/2024/04/jp-draws-US-Flag.webp" alt="USA Flag" class="flag-icon"></th>
                <th scope="col">Equivalent Value in ZiG <img src="https://zimpricecheck.com/wp-content/uploads/2024/04/tobias-Flag-of-Zimbabwe-scaled.webp" alt="Zimbabwe Flag" class="flag-icon"></th>
                </tr>
            </thead>
            <tbody>
    ';

    $table_rows = '';
    foreach ($zig_notes as $value => $note) {
        // Calculate equivalent value in USD
        $usd_equivalent = number_format($value * $rates['rates']['ZiG_Mid'], 2, '.', ' ');

        // Add row to the table
        $table_rows .= '
            <tr>
                <td>' . wp_kses_post($note) . '</td>
                <td>' . wp_kses_post($usd_equivalent) . ' ZiG</td>
            </tr>
        ';
    }

    // End building the HTML table
    $table_footer = '
            </tbody>
        </table></figure>
        <p><strong><em>Last updated ' . wp_kses_post($rates['rates']['updatedAt']) . '</em></strong></p>
    ';

    return '<div class="fuel-prices-table">' . $table_header . $table_rows . $table_footer . '</div>';
}
