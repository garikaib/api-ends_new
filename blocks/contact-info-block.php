<?php
use Carbon_Fields\Block;
use Carbon_Fields\Field;

add_action('carbon_fields_register_fields', 'crb_register_contact_info_block');
function crb_register_contact_info_block()
{
    Block::make(__('Contact Information'))
        ->add_fields(array(
            Field::make('checkbox', 'show_email', __('Show Email')),
            Field::make('checkbox', 'show_phone', __('Show Phone')),
        ))
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
            // Fetch contact details from theme options
            $contact_email = carbon_fields_get_theme_option('contact_email');
            $contact_phone = carbon_fields_get_theme_option('contact_phone');

            ?>
            <div class="contact-info-block">
                <h3>Contact Sales</h3>
                <?php if ($fields['show_email'] && $contact_email): ?>
                    <p><strong>Email:</strong> <a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a></p>
                <?php endif;?>

                <?php if ($fields['show_phone'] && $contact_phone): ?>
                    <p><strong>Phone:</strong> <a href="tel:<?php echo esc_attr($contact_phone); ?>"><?php echo esc_html($contact_phone); ?></a></p>
                <?php endif;?>
            </div>
            <?php
});
}