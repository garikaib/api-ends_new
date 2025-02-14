<?php
add_shortcode('zp-order-received', 'zp_received_order');

function zp_received_order($attr)
{

    $order = new ZIMAPI(ZIMAPI_BASE);

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
            return zp_process_unsuccessful_order();
        }

        if (!$response || !is_array($response) || !isset($response['success']) || $response['success'] != 'true') {
            return zp_process_unsuccessful_order();
        }

        return zp_process_received_order();

    } else if (!empty($innbucks) && !empty($order_num)) {
        // Set innbucks variable
        $innbucks = true;

        // Get order number
        $order_num = sanitize_text_field($order_num);
        $usd_amount = sanitize_text_field($usd_amount);
        return zp_process_received_innbucks($order_num, $usd_amount);
    } else {
        // Both token and payer_id empty
        return zp_process_unsuccessful_order();
    }

}

function zp_process_received_order()
{
    // Process the received order.

    wp_enqueue_style('zp_order_received_order-received', plugin_dir_url(__FILE__) . 'css/order-received.css'); // enqueue the style.css file

    ob_start(); // start output buffering

    ?>
    <div class="zp_order_received_container">
        <div class="zp_order_received_tick-circle">
            <i class="fas fa-check fa-3x"></i>
        </div>
        <div class="zp_order_received_heading">
            <h4>Order Confirmed!</h4>
        </div>
        <div class="zp_order_received_text">
            <p>Your order has been placed successfully.</p>
            <p>It will be delivered to you soon. Please check your email for details.</p>
        </div>
        <div class="zp_order_received_button">
            <a href="/usdairtime" class="zp_order_received_button-link">Continue Shopping</a>
        </div>
    </div>
    <?php

    return ob_get_clean(); // end output buffering and return HTML
}

function zp_process_unsuccessful_order()
{
    // Process the unsuccessful order.

    wp_enqueue_style('zp_order_unsuccessful_order-received', plugin_dir_url(__FILE__) . 'css/order-unsuccessful.css'); // enqueue the style.css file

    ob_start(); // start output buffering

    ?>
    <div class="zp_order_unsuccessful_container">
        <div class="zp_order_unsuccessful_tick-circle zp_order_unsuccessful_danger">
            <i class="fas fa-exclamation-circle fa-3x"></i>
        </div>
        <div class="zp_order_unsuccessful_heading">
            <h4>Order Unsuccessful!</h4>
        </div>
        <div class="zp_order_unsuccessful_text">
            <p>Sorry, your order could not be processed.</p>
            <p>Please check your payment details and try again.</p>
        </div>
        <div class="zp_order_unsuccessful_button">
            <a href="/usdairtime" class="zp_order_unsuccessful_button-link">Back to Shopping</a>
        </div>
    </div>
    <?php

    return ob_get_clean(); // end output buffering and return HTML
}

function zp_process_received_innbucks($order_num, $usd_amount)
{

    wp_enqueue_style('zp_innbucks_style', plugin_dir_url(__FILE__) . 'css/innbucks.css');

    ob_start();

    ?>

  <div class="zp_innbucks_container">

    <div class="zp_innbucks_tick-circle">
      <i class="fas fa-check fa-3x"></i>
    </div>

    <div class="zp_innbucks_heading">
      <h4>Order Received</h4>
    </div>


    <div class="zp_innbucks_text">
      <p>Your order #<?php echo $order_num; ?> has been received and is ready for processing.</p>
      <p>You have chosen to pay using InnBucks. Your order will be processed after InnBucks payment has been received.</p>
    </div>
    <h4>How to Complete Payment</h4>

<ul>
  <li>Send the USD <?php echo $usd_amount; ?> amount to 0772473953 using InnBucks.</li>
  <li>The confirmation should have the name Garikai B Dzoma.</li>
  <li>Send US$<?php echo $usd_amount; ?> as the message.</li>
  <li>Send a WhatsApp message with a screenshot of the payment to +263719157333.</li>
  <li>Your order will be delivered once payment is confirmed.</li>
</ul>
    <div class="zp_innbucks_button">
      <a href="/zesa" class="zp_innbucks_button-link">Place Another Order</a>
    </div>

  </div>

  <?php

    return ob_get_clean();

}