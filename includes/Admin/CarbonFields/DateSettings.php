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
        $safe_state_html = $this->get_legacy_status_html();

        Container::make('theme_options', __('Date Management', 'api-end'))
            ->set_page_parent('zpc-api')
            
            // Tab 1: Short Dates (The main list)
            ->add_tab(__('Short Dates', 'api-end'), [
                Field::make('html', 'short_dates_intro')
                    ->set_html('<p><strong>' . __('Manage Simple Date Appending', 'api-end') . '</strong><br>' . __('These posts will have " - [Month] [Year]" appended to their title.', 'api-end') . '</p>'),

                Field::make('complex', 'short_date_posts', __('Short Date Posts List', 'api-end'))
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
                            ->set_max(1),
                        Field::make('text', 'manual_id', __('Manual ID (Legacy/Backup)', 'api-end'))
                            ->set_attribute('type', 'number')
                            ->set_help_text(__('This ID is used if the "Post/Page" selector is empty (e.g. for deleted posts or test environments).', 'api-end'))
                    ])
                    ->set_layout('tabbed-horizontal')
                    // Logic: If post selected, show Checkmark + ID + Type. If Manual ID only, show Warning + ID.
                    ->set_header_template('
                        <% if (post && post.length > 0) { %>
                            <span style="color: #46b450; font-weight: bold;">&#10003; Active</span> <%- post[0].subtype %>: <%- post[0].id %>
                        <% } else if (manual_id) { %>
                            <span style="color: #dc3232; font-weight: bold;">&#9888; Ghost ID</span>: <%- manual_id %>
                        <% } else { %>
                            New Item
                        <% } %>
                    ')
                    ->set_default_value($legacy_defaults)
            ])

            // Tab 2: Inflation Dates
            ->add_tab(__('Inflation Dates', 'api-end'), [
                Field::make('html', 'inflation_intro')
                    ->set_html('<p><strong>' . __('Manage Inflation Date Ranges', 'api-end') . '</strong><br>' . __('These posts will have " (Start Date - Present)" appended.', 'api-end') . '</p>'),

                Field::make('complex', 'inflation_date_posts', __('Inflation Posts List', 'api-end'))
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
                            ->set_required(true),
                            
                        Field::make('text', 'custom_label_format', __('Custom Label Format', 'api-end')),
                    ])
                    ->set_default_value($inflation_defaults)
                    ->set_layout('tabbed-horizontal')
                    ->set_header_template('<%- post && post.length > 0 ? "Inflation Post: " + post[0].id : "New Item" %>')
            ])

            // Tab 3: Maintenance / Tools
            ->add_tab(__('Maintenance Tools', 'api-end'), [
                Field::make('html', 'restore_info', __('Restore Information', 'api-end'))
                    ->set_html('<p><strong>' . __('Legacy Data Restore', 'api-end') . '</strong><br>' . __('Use this tool to import the hardcoded "Safe State" IDs into the editable lists in the other tabs.', 'api-end') . '</p>'),

                Field::make('html', 'legacy_list_display', __('Safe State Viewer', 'api-end'))
                    ->set_html($safe_state_html),

                Field::make('separator', 'restore_actions', __('Import Actions', 'api-end')),

                Field::make('radio', 'import_mode', __('Import Mode', 'api-end'))
                    ->set_options([
                        'merge' => __('Merge (Add missing IDs to current list)', 'api-end'),
                        'overwrite' => __('Overwrite (Reset list to Safe State)', 'api-end'),
                    ])
                    ->set_default_value('merge'),

                Field::make('html', 'import_button_html')
                    ->set_html($this->get_import_button_html()),
            ]);
        
        // Register AJAX handler
        add_action('wp_ajax_zpc_import_legacy_dates', [$this, 'ajax_import_legacy']);
    }

    public function handle_save()
    {
        $should_import = carbon_get_theme_option('import_legacy_ids');
        
        if (!$should_import) {
            return;
        }

        // Re-calculate mappings in case logic changes
        $formatted_legacy_short = $this->get_formatted_legacy_short_dates();
        $formatted_legacy_inflation = $this->get_formatted_legacy_inflation();
        $mode = carbon_get_theme_option('import_mode');

        // 1. Handle Short Dates
        $current_short_dates = carbon_get_theme_option('short_date_posts') ?: [];
        
        if ($mode === 'overwrite') {
            $new_short_dates = $formatted_legacy_short;
        } else {
            // Merge logic for Complex Field
            // Check existence by Manual ID or Post ID
            $existing_ids = [];
            foreach ($current_short_dates as $item) {
                if (!empty($item['manual_id'])) {
                    $existing_ids[$item['manual_id']] = true;
                }
                if (!empty($item['post'])) {
                    $existing_ids[$item['post'][0]['id']] = true;
                }
            }
            
            $new_short_dates = $current_short_dates;
            foreach ($formatted_legacy_short as $item) {
                $check_id = $item['manual_id']; // Legacy items always have manual_id set
                if (!isset($existing_ids[$check_id])) {
                    $new_short_dates[] = $item;
                }
            }
        }
        carbon_set_theme_option('short_date_posts', $new_short_dates);

        // 2. Handle Inflation Dates
        $current_inflation = carbon_get_theme_option('inflation_date_posts') ?: [];
        
        if ($mode === 'overwrite') {
            $new_inflation = $formatted_legacy_inflation;
        } else {
            $legacy_id = '7647';
            $exists = false;
            foreach ($current_inflation as $item) {
                if (!empty($item['post']) && $item['post'][0]['id'] == $legacy_id) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists && !empty($formatted_legacy_inflation)) {
                $current_inflation[] = $formatted_legacy_inflation[0];
            }
            $new_inflation = $current_inflation;
        }
        carbon_set_theme_option('inflation_date_posts', $new_inflation);

        carbon_set_theme_option('import_legacy_ids', false);
    }
    
    private function get_formatted_legacy_short_dates() {
        $ids = $this->get_legacy_short_date_ids();
        $formatted = [];
        foreach ($ids as $id) {
            $post_type = get_post_type($id);
            // Even if post_type is null, we store manual_id.
            // If post exists, we also pre-fill the association.
            
            $entry = [
                'manual_id' => (string)$id,
                'post' => [],
            ];
            
            if ($post_type) {
                $entry['post'] = [
                    [
                        'value' => 'post:' . $post_type . ':' . $id,
                        'id' => (string)$id,
                        'type' => 'post',
                        'subtype' => $post_type,
                    ]
                ];
            }
            
            $formatted[] = $entry;
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

    private function get_legacy_status_html()
    {
        $ids = $this->get_legacy_short_date_ids();
        
        $html = '<div style="margin-bottom: 10px; font-weight: 600;">' . __('Legacy Safe State List (Scroll to view)', 'api-end') . ':</div>';
        $html .= '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #c3c4c7; padding: 0; background: #fff;">';
        $html .= '<table class="widefat fixed striped" style="border: none;">';
        $html .= '<thead><tr><th style="font-weight: bold;">ID</th><th style="font-weight: bold;">Safe State Status (Local Check)</th></tr></thead>';
        $html .= '<tbody>';
        
        foreach ($ids as $id) {
            $post = get_post($id);
            if ($post) {
                $status = '<span style="color: #007017; font-weight: 500;">&#10003; Found: ' . esc_html(mb_strimwidth($post->post_title, 0, 40, "...")) . '</span>';
            } else {
                $status = '<span style="color: #d63638;">&#10007; Not Found Locally</span>';
            }
            $html .= sprintf('<tr><td>%d</td><td>%s</td></tr>', $id, $status);
        }
        
        $html .= '</tbody></table></div>';
        
        return $html;
    }

    private function get_import_button_html()
    {
        $nonce = wp_create_nonce('zpc_import_legacy_dates');
        
        return '
        <div style="margin-top: 15px;">
            <button type="button" id="zpc-import-legacy-btn" class="button button-primary button-large">
                ' . __('Import Legacy IDs Now', 'api-end') . '
            </button>
            <span id="zpc-import-status" style="margin-left: 10px;"></span>
        </div>
        <p class="description">' . __('Click to immediately import the Safe State IDs into the Short Dates list. The page will reload after import.', 'api-end') . '</p>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $("#zpc-import-legacy-btn").on("click", function() {
                var $btn = $(this);
                var $status = $("#zpc-import-status");
                var mode = $("input[name=\"_import_mode\"]:checked").val() || "merge";
                
                $btn.prop("disabled", true);
                $status.html("<span style=\"color: #0073aa;\">Importing...</span>");
                
                $.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: {
                        action: "zpc_import_legacy_dates",
                        mode: mode,
                        _wpnonce: "' . $nonce . '"
                    },
                    success: function(response) {
                        if (response.success) {
                            $status.html("<span style=\"color: #46b450;\">✔ " + response.data.message + "</span>");
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            $status.html("<span style=\"color: #dc3232;\">✘ " + response.data.message + "</span>");
                            $btn.prop("disabled", false);
                        }
                    },
                    error: function() {
                        $status.html("<span style=\"color: #dc3232;\">✘ AJAX Error</span>");
                        $btn.prop("disabled", false);
                    }
                });
            });
        });
        </script>';
    }

    public function ajax_import_legacy()
    {
        check_ajax_referer('zpc_import_legacy_dates', '_wpnonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'api-end')]);
        }

        $mode = isset($_POST['mode']) ? sanitize_text_field($_POST['mode']) : 'merge';
        
        $formatted_legacy_short = $this->get_formatted_legacy_short_dates();
        $formatted_legacy_inflation = $this->get_formatted_legacy_inflation();

        // 1. Handle Short Dates
        $current_short_dates = carbon_get_theme_option('short_date_posts') ?: [];
        
        if ($mode === 'overwrite') {
            $new_short_dates = $formatted_legacy_short;
        } else {
            $existing_ids = [];
            foreach ($current_short_dates as $item) {
                if (!empty($item['manual_id'])) {
                    $existing_ids[$item['manual_id']] = true;
                }
                if (!empty($item['post']) && !empty($item['post'][0]['id'])) {
                    $existing_ids[$item['post'][0]['id']] = true;
                }
            }
            
            $new_short_dates = $current_short_dates;
            foreach ($formatted_legacy_short as $item) {
                $check_id = $item['manual_id'];
                if (!isset($existing_ids[$check_id])) {
                    $new_short_dates[] = $item;
                }
            }
        }
        carbon_set_theme_option('short_date_posts', $new_short_dates);

        // 2. Handle Inflation Dates
        $current_inflation = carbon_get_theme_option('inflation_date_posts') ?: [];
        
        if ($mode === 'overwrite') {
            $new_inflation = $formatted_legacy_inflation;
        } else {
            $legacy_id = '7647';
            $exists = false;
            foreach ($current_inflation as $item) {
                if (!empty($item['post']) && !empty($item['post'][0]['id']) && $item['post'][0]['id'] == $legacy_id) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists && !empty($formatted_legacy_inflation)) {
                $current_inflation[] = $formatted_legacy_inflation[0];
            }
            $new_inflation = $current_inflation;
        }
        carbon_set_theme_option('inflation_date_posts', $new_inflation);

        $count = count($new_short_dates);
        wp_send_json_success(['message' => sprintf(__('Imported %d Short Date entries. Reloading...', 'api-end'), $count)]);
    }
}
