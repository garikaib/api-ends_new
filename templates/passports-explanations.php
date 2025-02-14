<?php

//Output introduction
function passportIntro()
{
    return '<p>The process of obtaining a passport in Zimbabwe involves certain fees and steps that applicants need to follow. As of ' . date('F Y') . ', the Zimbabwean government has updated the passport application fees to streamline the process and enhance the security features of passports. This guide provides a comprehensive overview of the current passport fees, compares them with neighboring SADC countries, and answers frequently asked questions to help you navigate the application process smoothly.
    </p>';
}
//Add shortcode to output introduction
add_shortcode('passport-intro', 'passportIntro');

//Other countries
function zp_show_sadc_passport_fees($attr)
{

    $zim_rates = new ZIMAPI(ZIMAPI_BASE);
    $endpoints = [
        'data' => [
            'endpoint' => '/fees/passport-sadc',
        ],
        'oe_rates' => [
            'endpoint' => '/rates/oe-rates/raw',
        ],
    ];
    try {

        $data = $zim_rates->multiCallApi($endpoints, zp_get_remote_ip());

        // require_once plugin_dir_path(__FILE__) . 'templates/rates.php';
        return zp_build_sadc_table($data['data']['data'], $data['oe_rates']['data']);
    } catch (Exception $e) {
        // Log the error
        error_log('Error the latest Exchange Rates: ' . $e->getMessage());
        // Return an error message to the user
        require_once plugin_dir_path(__FILE__) . 'includes/class-show-notice.php';

        return ZP_SHOW_NOTICE::showError("We couldn't retrieve the latest Exchange rates at the moment. Please try again later.");
    }

}
add_shortcode('sadc-passport-fees', 'zp_show_sadc_passport_fees');

function zp_build_sadc_table(array $data, array $oe_rates = [])
{
    // Create a mapping of currency codes to exchange rates
    $exchangeRates = [];
    foreach ($oe_rates['rates'] as $rate) {
        error_log(print_r($rate, true));
        if (is_array($rate)) {
            foreach ($rate as $value) {
                // Check if value is an array
                if (is_array($value)) {
                    foreach ($value as $currency => $item) {
                        error_log(print_r($currency, true));
                        $exchangeRates[$currency] = $item;
                    }

                }
            }
        }

    }

    // Start building the HTML table
    $html = '<figure class="wp-block-table"><table class="has-fixed-layout"><thead><tr><th>Country</th><th>Ordinary Passport Fee (USD)</th><th>Emergency Passport Fee (USD)</th></tr></thead><tbody>';

    // Loop through the data to populate the table rows
    foreach ($data['prices']['fees'] as $fee) {
        $country = $fee['country'];
        $normal = $fee['normal'];
        $emergency = isset($fee['emergency']) ? $fee['emergency'] : 'N/A';
        $currency_code = $fee['currency_code'];

        // Convert the normal and emergency fees to USD
        if (isset($exchangeRates[$currency_code])) {
            $normal_usd = $normal / $exchangeRates[$currency_code];
            $normal_usd = 'US$' . number_format($normal_usd, 2);

            if ($emergency !== 'N/A') {
                $emergency_usd = $emergency / $exchangeRates[$currency_code];
                $emergency_usd = 'US$' . number_format($emergency_usd, 2);
            } else {
                $emergency_usd = 'N/A';
            }
        } else {
            $normal_usd = 'N/A';
            $emergency_usd = 'N/A';
        }

        // Append the row to the HTML table
        $html .= "<tr><td>{$country}</td><td>{$normal_usd}</td><td>{$emergency_usd}</td></tr>";
    }

    // Close the HTML table
    $html .= '</tbody></table></figure>';

    return $html;
}
/*
function showPassportFAQs()
{
return "
<h3>Frequently Asked Questions (FAQs)</h3

 **1. What are the benefits of the new Zimbabwean e-passport?**
- The new e-passport contains an RFID chip, biometric data, and enhanced security features, making it more secure and durable. It also offers a longer validity period and can be used for visa-free travel to certain countries.

 **2. How can I apply for a new Zimbabwean passport?**
- To apply for a new Zimbabwean passport, you need to visit a passport office in Zimbabwe with the following documents:
- A completed passport application form
- Proof of citizenship (such as a birth certificate or national ID card)
- Proof of identity (such as a driver’s license or passport)
- Two passport-sized photos

 **3. How long does it take to get a new Zimbabwean passport?**
- It typically takes around 5-7 working days to get a new Zimbabwean passport, depending on the type of passport you are applying for (e.g., ordinary passport, emergency passport).

 **4. Can I use my old Zimbabwean passport to travel?**
- Yes, you can still use your old Zimbabwean passport as long as it is still valid. However, it is recommended to apply for a new e-passport as soon as possible due to its enhanced security features.

 **5. How much does a Zimbabwean e-passport cost?**
- The cost of a Zimbabwean e-passport varies depending on the type of passport you are applying for. The ordinary passport costs US$150.00, while the emergency passport costs US$250.00.

 **6. Where can I pay the passport fees?**
- Payments for passports can be made at CBZ and First Capital banks. Ensure you have proof of payment when submitting your application.

 **7. What should I do if my passport application is rejected?**
- If your passport application is rejected, you should receive a notification explaining the reason. You may need to correct any issues and reapply, ensuring all required documents and fees are submitted correctly.

 **8. Are there any additional fees for passport services?**
- Yes, an additional fee of US$20.00 is charged for every electronically readable passport application to obtain a quick response (QR) code.

 **9. Can I expedite my passport application?**
- Yes, you can apply for an emergency passport which costs US$250.00. This service is intended for urgent travel needs and offers a faster processing time compared to ordinary passports.

 **10. How can I track the status of my passport application?**
- You can track the status of your passport application by visiting the passport office where you applied or by contacting them directly for updates.

---

### Conclusion

Obtaining a passport in Zimbabwe involves a straightforward process, provided you have all the necessary documents and fees ready. With the introduction of the new e-passport, Zimbabwean nationals can enjoy enhanced security features, greater durability, and the convenience of faster border crossings. Stay informed and ensure you have the most up-to-date information to facilitate your travel plans.

---

This guide is designed to help you understand the current passport application fees and process in Zimbabwe. Should you have any further questions, feel free to reach out to the relevant authorities or consult additional resources.";
}
 */
