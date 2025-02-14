<?php

function addClearCache()
{
    register_rest_route(
        'cache-clear',
        '/purge',
        [
            'methods' => 'GET',
            'callback' => 'clearAPICache',
            'permission_callback' => '__return_true',
        ]
    );
    register_rest_route(
        'zimprice',
        '/update-post',
        [
            'methods' => 'GET',
            'callback' => 'updatePostDate',
            'permission_callback' => '__return_true',
        ]
    );
}

add_action('rest_api_init', 'addClearCache');
/*
function addClearCache()
{
register_rest_route(
'cache-clear',
'/purge',
[
'methods' => 'GET',
'callback' => 'clearAPICache',
'permission_callback' => 'rest_authenticate_request',
]
);
register_rest_route(
'zimprice',
'/update-post',
[
'methods' => 'GET',
'callback' => 'updatePostDate',
'permission_callback' => 'rest_authenticate_request',
]
);
}
add_action('rest_api_init', 'addClearCache');
 */
function clearAPICache(WP_REST_Request $request)
{
    $data = $request->get_params();
    // error_log(print_r($data, true));
    if (array_key_exists('token', $data)) {
        if ($data['token'] == zimpriceAuthUser()) {
            error_log("Everything is in order purge the cache!");
            //Purge whole cache after price update
            do_action("swcfpc_purge_cache");
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
        error_log("Something went wrong, cannot purge cache!");
        return new WP_REST_Response([
            'error_message' => '',
            'success' => false,
            'error' => true,
        ], 403);
    }
}

function updatePostDate(WP_REST_Request $request)
{
    try {
        $data = $request->get_params();
        // error_log(print_r($data, true));
        if (array_key_exists('token', $data) and array_key_exists("post_id", $data)) {
            if ($data['token'] == zimpriceAuthUser()) {
                error_log("Updating Post");
                //Purge whole cache after price update
                $time = current_time('mysql');
                $result = wp_update_post(
                    array(
                        'ID' => $data['post_id'], // ID of the post to update
                        'post_date' => $time,
                        'post_date_gmt' => get_gmt_from_date($time),
                    )
                );
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
function zimpriceAuthUser()
{
    return "secret321";
}
