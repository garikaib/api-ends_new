<?php
add_shortcode('zp-test-order', 'zp_test_received_order');

function zp_test_received_order($attr)
{
    // Check if the user is logged in and is an admin
    if (!current_user_can('manage_options')) {
        // If not, redirect to the home page
        wp_redirect(home_url());
        exit;
    }

    $order = new ZIMAPI(ZIMAPI_TEST_BASE);

    $token = get_query_var('token');
    $payer_id = get_query_var('PayerID');
    $innbucks = get_query_var('innbucks', false);
    $order_num = get_query_var('order_num');
    $usd_amount = get_query_var('usd_amount');

    if (!empty($token) && !empty($payer_id)) {
        $token = sanitize_text_field($token);
        $payer_id = sanitize_text_field($payer_id);

        try {
            $response = $order->callApi('/orders/token/' . $token . '/' . $payer_id . '/', zp_get_remote_ip(), null, 'POST');
            error_log(print_r($response, true));
        } catch (Exception $e) {
            error_log($e->getMessage());
            return zp_test_process_unsuccessful_order();
        }

        if (!$response || !is_array($response) || !isset($response['success']) || $response['success'] != 'true') {
            return zp_test_process_unsuccessful_order();
        }

        return zp_test_process_received_order();

    } else if (!empty($innbucks) && !empty($order_num)) {
        $innbucks = true;
        $order_num = sanitize_text_field($order_num);
        $usd_amount = sanitize_text_field($usd_amount);
        return zp_test_process_received_innbucks($order_num, $usd_amount);
    } else {
        return zp_test_process_unsuccessful_order();
    }
}

function zp_test_process_received_order()
{
    wp_enqueue_style('zp_order_received_order-received', plugin_dir_url(__FILE__) . 'css/order-received.css');

    ob_start();
    ?>
    <div class="zp_order_received_container">
        <div class="zp_order_received_tick-circle">
            <i class="fas fa-check fa-3x"></i>
        </div>
        <div class="zp_order_received_heading">
            <h4>Test Order Confirmed!</h4>
        </div>
        <div class="zp_order_received_text">
            <p>Your test order has been placed successfully.</p>
            <p>This is a test order and no actual delivery will occur. Please check your email for details.</p>
        </div>
        <div class="zp_order_received_button">
            <a href="/usdairtime" class="zp_order_received_button-link">Continue Testing</a>
        </div>
    </div>
    <?php
return ob_get_clean();
}

function zp_test_process_unsuccessful_order()
{
    wp_enqueue_style('zp_order_unsuccessful_order-received', plugin_dir_url(__FILE__) . 'css/order-unsuccessful.css');

    ob_start();
    ?>
    <div class="zp_order_unsuccessful_container">
        <div class="zp_order_unsuccessful_tick-circle zp_order_unsuccessful_danger">
            <i class="fas fa-exclamation-circle fa-3x"></i>
        </div>
        <div class="zp_order_unsuccessful_heading">
            <h4>Test Order Unsuccessful!</h4>
        </div>
        <div class="zp_order_unsuccessful_text">
            <p>Sorry, your test order could not be processed.</p>
            <p>Please check your test payment details and try again.</p>
        </div>
        <div class="zp_order_unsuccessful_button">
            <a href="/usdairtime" class="zp_order_unsuccessful_button-link">Back to Test Shopping</a>
        </div>
    </div>
    <?php
return ob_get_clean();
}

function zp_test_process_received_innbucks($order_num, $usd_amount)
{
    wp_enqueue_style('zp_innbucks_style', plugin_dir_url(__FILE__) . 'css/innbucks.css');

    ob_start();
    ?>
    <div class="zp_innbucks_container">
        <div class="zp_innbucks_tick-circle">
            <i class="fas fa-check fa-3x"></i>
        </div>
        <div class="zp_innbucks_heading">
            <h4>Test Order Received</h4>
        </div>
        <div class="zp_innbucks_text">
            <p>Your test order #<?php echo $order_num; ?> has been received and is ready for processing.</p>
            <p>You have chosen to pay using InnBucks in this test scenario. Your test order will be processed after simulated InnBucks payment has been received.</p>
        </div>
        <h4>How to Complete Test Payment</h4>
        <ul>
            <li>This is a test order. No actual payment should be made.</li>
            <li>In a real scenario, you would send USD <?php echo $usd_amount; ?> to 0772473953 using InnBucks.</li>
            <li>The confirmation would have the name Garikai B Dzoma.</li>
            <li>You would send US$<?php echo $usd_amount; ?> as the message.</li>
            <li>You would send a WhatsApp message with a screenshot of the payment to +263719157333.</li>
            <li>Your order would be delivered once payment is confirmed.</li>
        </ul>
        <div class="zp_innbucks_button">
            <a href="/zesa" class="zp_innbucks_button-link">Place Another Test Order</a>
        </div>
    </div>
    <?php
return ob_get_clean();
}