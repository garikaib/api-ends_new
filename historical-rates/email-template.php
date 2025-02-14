<?php

function generate_email_template($subject, $message, $post_id = null, $type = "success")
{
    $site_name = get_bloginfo('name');
    $logo_url = wp_get_attachment_url(carbon_get_theme_option('site_logo')); // Fetch logo URL from Carbon
    $year = date('Y');

    $post_link = '';
    if ($post_id) {
        $post_link = "<p><a href='" . get_permalink($post_id) . "' class='button'>View Created Post</a></p>";
    }

    $template = <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{$subject}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
            }
            .container {
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                max-width: 600px;
                margin: auto;
                padding: 20px;
            }
            .header {
                text-align: center;
                padding-bottom: 20px;
            }
            .header img {
                max-width: 200px;
            }
            .content {
                font-size: 16px;
                color: #555;
                margin: 10px 0;
            }
            .footer {
                text-align: center;
                font-size: 14px;
                color: #aaa;
                margin-top: 20px;
            }
            .highlight {
                color: #FFA500; /* Bright Orange */
                font-weight: bold;
            }
            .button {
                display: inline-block;
                padding: 10px 20px;
                background-color: #FFA500; /* Bright Orange */
                color: #ffffff;
                text-decoration: none;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="{$logo_url}" alt="{$site_name}">
            </div>
            <div class="content">
                <h2>{$subject}</h2>
                <p>{$message}</p>
                {$post_link}
            </div>
            <div class="footer">
                &copy; {$year} {$site_name}. All rights reserved.
            </div>
        </div>
    </body>
    </html>
HTML;

    return $template;
}
