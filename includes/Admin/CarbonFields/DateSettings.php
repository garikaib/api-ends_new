<?php

namespace ZimPriceCheck\ApiEnds\Admin\CarbonFields;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class DateSettings
{
    public function __construct()
    {
        $this->register();
        add_action('carbon_fields_theme_options_container_saved', [$this, 'handle_save']);
    }

    public function register()
    {
        $legacy_defaults = $this->get_formatted_legacy_short_dates();
        $inflation_defaults = $this->get_formatted_legacy_inflation();

        Container::make('theme_options', __('Date Management', 'api-end'))
            ->set_page_parent('zpc-api') // Under Zimpricecheck API
            ->add_fields([
                Field::make('separator', 'restore_separator', __('Legacy Import / Restore', 'api-end')),
                
                Field::make('html', 'restore_html', __('Restore Information', 'api-end'))
                    ->set_html('<p>' . __('Use this section to import the original hardcoded list of posts. Useful for initial setup or restoring defaults.', 'api-end') . '</p>'),

                Field::make('checkbox', 'import_legacy_ids', __('Import Legacy IDs', 'api-end'))
                    ->set_option_value('yes')
                    ->set_help_text(__('Check this and save to import the legacy hardcoded IDs into the fields below.', 'api-end')),

                Field::make('radio', 'import_mode', __('Import Mode', 'api-end'))
                    ->set_options([
                        'merge' => __('Merge (Add missing legacy IDs to current list)', 'api-end'),
                        'overwrite' => __('Overwrite (Replace current list with legacy IDs)', 'api-end'),
                    ])
                    ->set_default_value('merge')
                    ->set_conditional_logic([
                        [
                            'field' => 'import_legacy_ids',
                            'value' => true,
                        ]
                    ]),

                Field::make('separator', 'fields_separator', __('Date Configuration', 'api-end')),

                Field::make('association', 'short_date_posts', __('Short Date Posts (Month Year)', 'api-end'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'post',
                        ],
                        [
                            'type' => 'post',
                            'post_type' => 'page',
                        ]
                    ])
                    ->set_default_value($legacy_defaults)
                    ->set_help_text(__('Select posts/pages to append " - [Month] [Year]" to the title.', 'api-end')),

                Field::make('complex', 'inflation_date_posts', __('Inflation Date Posts (Range)', 'api-end'))
                    ->add_fields([
                        Field::make('association', 'post', __('Post/Page', 'api-end'))
                            ->set_types([
                                [
                                    'type' => 'post',
                                    'post_type' => 'post',
                                ],
                                [
                                    'type' => 'post',
                                    'post_type' => 'page',
                                ]
                            ])
                            ->set_max(1)
                            ->set_required(true),
                        
                        Field::make('date', 'start_date', __('Start Date', 'api-end'))
                            ->set_storage_format('Y-m-d')
                            ->set_input_format('F j, Y', 'php')
                            ->set_required(true)
                            ->set_attribute('placeholder', 'Select start date'),
                            
                        Field::make('text', 'custom_label_format', __('Custom Label Format (Optional)', 'api-end'))
                            ->set_help_text(__('Leave empty for default: " (Start Month Year - Current Month Year)"', 'api-end')),
                    ])
                    ->set_default_value($inflation_defaults)
                    ->set_layout('tabbed-horizontal')
                    ->set_header_template('<%- post ? "Post ID: " + post[0].id : "New Item" %>') 
            ]);
    }

    public function handle_save()
    {
        // Check if we need to do anything
        $should_import = carbon_get_theme_option('import_legacy_ids');
        
        if (!$should_import) {
            return;
        }

        $mode = carbon_get_theme_option('import_mode');
        // Use helper to get formatted arrays directly
        $formatted_legacy_short = $this->get_formatted_legacy_short_dates();
        $formatted_legacy_inflation = $this->get_formatted_legacy_inflation();

        // 1. Handle Short Dates
        $current_short_dates = carbon_get_theme_option('short_date_posts') ?: [];
        
        if ($mode === 'overwrite') {
            $new_short_dates = $formatted_legacy_short;
        } else {
            // Merge
            // Index existing by ID to avoid duplicates
            $existing_map = [];
            foreach ($current_short_dates as $item) {
                $existing_map[$item['id']] = $item;
            }
            
            foreach ($formatted_legacy_short as $item) {
                if (!isset($existing_map[$item['id']])) {
                    $existing_map[$item['id']] = $item;
                }
            }
            $new_short_dates = array_values($existing_map);
        }
        
        carbon_set_theme_option('short_date_posts', $new_short_dates);

        // 2. Handle Inflation Dates
        $current_inflation = carbon_get_theme_option('inflation_date_posts') ?: [];
        
        if ($mode === 'overwrite') {
            $new_inflation = $formatted_legacy_inflation;
        } else {
            // Merge: Check if legacy ID (7647) already exists
            $legacy_id = '7647';
            $exists = false;
            foreach ($current_inflation as $item) {
                if (!empty($item['post']) && $item['post'][0]['id'] == $legacy_id) {
                    $exists = true;
                    break;
                }
            }
            
            $new_inflation = $current_inflation;
            if (!$exists && !empty($formatted_legacy_inflation)) {
                $new_inflation[] = $formatted_legacy_inflation[0];
            }
        }
        carbon_set_theme_option('inflation_date_posts', $new_inflation);

        // 3. Reset the checkbox
        carbon_set_theme_option('import_legacy_ids', false);
    }
    
    private function get_formatted_legacy_short_dates() {
        $ids = $this->get_legacy_short_date_ids();
        $formatted = [];
        foreach ($ids as $id) {
            // Attempt to get real post type, fallback to 'post' if missing (for test sites)
            $post_type = get_post_type($id) ?: 'post';
            $formatted[] = [
                'value' => 'post:' . $post_type . ':' . $id,
                'id' => (string)$id,
                'type' => 'post',
                'subtype' => $post_type,
            ];
        }
        return $formatted;
    }
    
    private function get_formatted_legacy_inflation() {
        $id = 7647;
        $start_date = '2018-06-01';
        $post_type = get_post_type($id) ?: 'post';
        
        return [
            [
                'post' => [
                    [
                        'value' => 'post:' . $post_type . ':' . $id,
                        'id' => (string)$id,
                        'type' => 'post',
                        'subtype' => $post_type,
                    ]
                ],
                'start_date' => $start_date,
                'custom_label_format' => '',
            ]
        ];
    }

    private function get_legacy_short_date_ids()
    {
        return [
            4887, // Groceries
            9506, // BancABC
            9729, // Nostro Account Opening
            10051, // Harare Parking Fees
            10534, // COVID test centres
            5828, // agric_inputs:
            9518, // deltaa:
            8965, // econet:
            15760, // Econet Smart USD bundles
            9861, // Ecocash Fees
            10456, // OneMoney Fees
            10385, // Telecash Fees
            10075, // telecel:
            5325, // exchange_rates:
            5321, // fuel_gas:
            9709, // liquid:
            13874, // Lamasat
            5281, // mbare:
            10131, // telone:
            10696, // tollgates:
            10078, // utande:
            11445, // zbc_radio_tv:
            9870, // zesa:
            10895, // zupco:
            1235, // Mukuru
            6341, // Passports
            11374, // Traffic safety Council of Zimbabwe
            12965, // Vehicle ownership fees
            9417, // Ways to send money to Zimbabwe
            12316, // Western Union
            12319, // World Remit
            12711, // ZESA calculator
            9344, // NetOne bundles
            16649, // Fines
            17167, // Births and Deaths new
            18785, // LP GAs Prices
            18797, // Traffic Fines
        ];
    }
}
