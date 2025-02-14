<?php

function zpUpdateRatePosts(WP_REST_Request $request)
{
    try {
        $data = $request->get_params();
        // error_log(print_r($data, true));
        if (array_key_exists('token', $data) and array_key_exists("post_id", $data) and array_key_exists("day", $data)) {
            if ($data['token'] == zimpriceAuthUser()) {
                error_log("Updating Post");
                //Purge whole cache after price update
                $time = current_time('mysql');
                $result = zp_publish_rates($data['day']);
                if ($result) {
                    error_log("Post updated");
                }

                return new WP_REST_Response([
                    'error_message' => '',
                    'success' => true,
                    'error' => false,
                ], 200);
            } else {
                error_log("Auth error!");
                return new WP_REST_Response([
                    'error_message' => 'Aunthenticate first!',
                    'success' => false,
                    'error' => true,
                ], 403);
            }
        } else {
            error_log("Couldn't update post for some reason");
            return new WP_REST_Response([
                'error_message' => '',
                'success' => false,
                'error' => true,
            ], 403);
        }
    } catch (Exception $e) {
        error_log(print_r($e, true));
        return new WP_REST_Response([
            'error_message' => '',
            'success' => false,
            'error' => true,
        ], 403);
    }
}

function zp_publish_rates(string $day)
{

    $date = $date = DateTime::createFromFormat('Y-m-d', $day);
    $result = false;

    $p_title = 'Black Market Exchange Rates ' . $date->format('l, d F Y');
    $slug = $date->format("Y-m-d");
    $post_data = array(
        'post_title' => wp_strip_all_tags($p_title),
        'post_content' => '',
        'post_status' => 'publish',
        'post_type' => 'zwl-rates',
        'post_author' => '1',
        'post_name' => $slug,
        // 'post_category' => POST_CATEGORY,
        // 'page_template' => POST_TEMPLATE
    );
    if (!is_admin()) {
        require_once ABSPATH . 'wp-admin/includes/post.php';
    }
    if (!post_exists($p_title)) {
        $result = wp_insert_post($post_data);
        error_log($result);
    } else {
        error_log("Post already exists backing off!");
        $result = true;
    }
    return $result;
}
