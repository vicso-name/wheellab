<?php

defined('ABSPATH') || exit;

add_action('wp_ajax_wheellab_mailchimp_subscribe', 'wheellab_ajax_mailchimp_subscribe');
add_action('wp_ajax_nopriv_wheellab_mailchimp_subscribe', 'wheellab_ajax_mailchimp_subscribe');

function wheellab_ajax_mailchimp_subscribe(): void {
    check_ajax_referer('wheellab_mailchimp_subscribe', 'nonce');

    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';

    if (!$email || !is_email($email)) {
        wp_send_json_error(['message' => __('Please enter a valid email address.', 'wheellab')], 400);
    }

    $page_id = wheellab_get_blog_template_page_id();

    $api_key      = $page_id ? (string) get_field('subscribe_mailchimp_api_key', $page_id) : '';
    $audience_id  = $page_id ? (string) get_field('subscribe_mailchimp_audience_id', $page_id) : '';
    $double_optin = $page_id ? (bool) get_field('subscribe_mailchimp_double_optin', $page_id) : true;

    if (!$api_key || !$audience_id) {
        wp_send_json_error(['message' => __('Newsletter sign-up is not configured yet.', 'wheellab')], 503);
    }

    $result = wheellab_mailchimp_subscribe($api_key, $audience_id, $email, $double_optin);

    if ($result['success']) {
        wp_send_json_success(['message' => $result['message']]);
    }

    wp_send_json_error(['message' => $result['message']], $result['status']);
}

function wheellab_get_blog_template_page_id(): int {
    static $page_id = null;

    if ($page_id !== null) {
        return $page_id;
    }

    $pages = get_posts([
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'template-blog.php',
        'fields'         => 'ids',
    ]);

    $page_id = $pages ? (int) $pages[0] : 0;

    return $page_id;
}

function wheellab_mailchimp_subscribe(string $api_key, string $audience_id, string $email, bool $double_optin): array {
    if (!preg_match('/-([a-z0-9]+)$/i', $api_key, $matches)) {
        return [
            'success' => false,
            'message' => __('Newsletter sign-up is not configured correctly.', 'wheellab'),
            'status'  => 500,
        ];
    }

    $datacenter = $matches[1];
    $endpoint   = "https://{$datacenter}.api.mailchimp.com/3.0/lists/{$audience_id}/members";

    $response = wp_remote_post($endpoint, [
        'timeout' => 15,
        'headers' => [

            'Authorization' => 'Basic ' . base64_encode('wheellab:' . $api_key),
            'Content-Type'  => 'application/json',
        ],
        'body' => wp_json_encode([
            'email_address' => $email,

            'status'        => $double_optin ? 'pending' : 'subscribed',
        ]),
    ]);

    if (is_wp_error($response)) {
        return [
            'success' => false,
            'message' => __('Something went wrong. Please try again later.', 'wheellab'),
            'status'  => 502,
        ];
    }

    $code = (int) wp_remote_retrieve_response_code($response);

    if ($code === 200) {
        return [
            'success' => true,
            'message' => $double_optin
                ? __('Almost there! Check your inbox to confirm your subscription.', 'wheellab')
                : __('Thanks for subscribing!', 'wheellab'),
        ];
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (($body['title'] ?? '') === 'Member Exists') {
        return [
            'success' => false,
            'message' => __('This email is already subscribed.', 'wheellab'),
            'status'  => 409,
        ];
    }

    return [
        'success' => false,
        'message' => __('Something went wrong. Please try again later.', 'wheellab'),
        'status'  => 400,
    ];
}
