<?php
/**
 * Template part for displaying traffic fines.
 *
 * @var array $data The traffic fines data.
 */

require_once API_END_BASE . "includes/format-prices.php";
require_once API_END_BASE . "includes/dates.php";

$date = zp_today_full_date();
$fines = isset($data['fines']) ? $data['fines'] : [];

// Map shortcode types to actual categories
$category_map = [
    'licencing' => 'Licencing Offences',
    'moving' => 'Moving Offences',
    'signals' => 'Road Signs, Signals And Markings ’ Offences',
    'public' => 'Public Services Vehicle Offences',
    'speeding' => 'Speeding Offences',
];

// Map shortcode types to optimized headings
$heading_map = [
    'licencing' => 'Vehicle Licencing Fines',
    'moving' => 'Moving Traffic Offences Fines',
    'signals' => 'Road Signs & Signals Fines',
    'public' => 'Public Service Vehicle (PSV) Fines',
    'speeding' => 'Speeding Fines',
];

// Create a lookup for Category Name -> Heading
$category_to_heading = [];
foreach ($category_map as $key => $val) {
    if (isset($heading_map[$key])) {
        $category_to_heading[$val] = $heading_map[$key];
    }
}

$groups = [];
$main_heading = 'Traffic Fines';
$show_subheadings = false;

if (!empty($type) && isset($category_map[strtolower($type)])) {
    // Specific category requested
    $filter_category = $category_map[strtolower($type)];
    if (isset($heading_map[strtolower($type)])) {
        $main_heading = $heading_map[strtolower($type)];
    }
    
    $filtered_fines = array_filter($fines, function ($fine) use ($filter_category) {
        return $fine['category'] === $filter_category;
    });
    
    if (!empty($filtered_fines)) {
        $groups[$filter_category] = $filtered_fines;
    }
} else {
    // Show all, grouped by category
    $show_subheadings = true;
    foreach ($fines as $fine) {
        $cat = $fine['category'];
        if (!isset($groups[$cat])) {
            $groups[$cat] = [];
        }
        $groups[$cat][] = $fine;
    }
    // Sort groups by category name or custom order if needed
    ksort($groups);
}

?>

<div class="traffic-fines-table">
    <h4><?php echo esc_html($main_heading); ?> in Zimbabwe on <?php echo esc_html($date); ?></h4>

    <?php if (empty($groups)) : ?>
        <p>No traffic fines found.</p>
    <?php else : ?>
        <?php foreach ($groups as $category_name => $group_fines) : 
            $table_heading = isset($category_to_heading[$category_name]) ? $category_to_heading[$category_name] : $category_name;
        ?>
            <?php if ($show_subheadings) : ?>
                <h5 style="margin-top: 30px; margin-bottom: 15px; color: var(--wp--preset--color--primary, #333);"><?php echo esc_html($table_heading); ?></h5>
            <?php endif; ?>

            <figure class="wp-block-table is-style-stripes">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Description</th>
                            <th scope="col">Fine (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($group_fines as $fine) : ?>
                            <tr>
                                <td scope="row"><?php echo esc_html($fine['description']); ?></td>
                                <td><?php echo zp_format_prices($fine['fine_amount_usd'], 'usd'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (!$show_subheadings) : // Only show caption on the single table view or at the very end? Let's keep it per table or just once. 
                    // Actually, if we have multiple tables, repeating the caption might be noisy. Let's put it once at the bottom of the wrapper.
                endif; ?>
            </figure>
        <?php endforeach; ?>
        
        <figcaption class="wp-element-caption" style="text-align: center; margin-top: 10px; display: block;">Traffic fines in Zimbabwe.</figcaption>
    <?php endif; ?>

    <p>
        <strong><em>Last updated <?php echo isset($data['date']) ? esc_html(date('F j, Y', strtotime($data['date']))) : ''; ?></em></strong>
    </p>
</div>
