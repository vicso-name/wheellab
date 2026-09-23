<?php

defined('ABSPATH') || exit;

add_action('wp_ajax_wheellab_contact_webhook', 'wheellab_ajax_contact_webhook');
add_action('wp_ajax_nopriv_wheellab_contact_webhook', 'wheellab_ajax_contact_webhook');

function wheellab_ajax_contact_webhook(): void {
    check_ajax_referer('wheellab_contact_webhook', 'nonce');

    $webhook_url = (string) get_field('webhook_url', 'option');
    if (!$webhook_url) {
        // Nothing configured in Theme Options → Contact — silently no-op so
        // the CF7 submission itself (already handled) isn't affected.
        wp_send_json_success();
    }

    $payload = json_decode(wp_unslash($_POST['payload'] ?? ''), true);
    if (!is_array($payload)) {
        wp_send_json_error(['message' => 'Invalid payload'], 400);
    }

    $fields = is_array($payload['fields'] ?? null) ? $payload['fields'] : [];
    $field  = static fn (string $name): string => sanitize_text_field((string) ($fields[$name] ?? ''));

    $body = [
        'Name'               => trim($field('first-name') . ' ' . $field('last-name')),
        'Email'              => sanitize_email((string) ($fields['your-email'] ?? '')),
        'Phone'              => $field('your-phone'),
        'Comment'            => sanitize_textarea_field((string) ($fields['your-message'] ?? '')),
        'PreferredMessenger' => $field('preferred-messenger'),
        'MessengerId'        => $field('messenger-id'),
        'Budget'             => $field('budget'),
        'Industry'           => $field('industry'),
        'UserJourney'        => wheellab_sanitize_webhook_value($payload['userJourney'] ?? [], 200),
        'visitData'          => wheellab_sanitize_webhook_value($payload['visitData'] ?? []),
        'timezone'           => sanitize_text_field((string) ($payload['timezone'] ?? '')),
        'userGaId'           => !empty($payload['userGaId']) ? sanitize_text_field((string) $payload['userGaId']) : null,
    ];

    $response = wp_remote_post($webhook_url, [
        'timeout' => 15,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => wp_json_encode($body),
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Webhook request failed'], 502);
    }

    wp_send_json_success();
}

/**
 * Recursively sanitizes a value decoded from the client-side tracking
 * payload (localStorage journey/UTM data) before it's forwarded off-site.
 * $max_items caps array length as a server-side backstop to the client's
 * own cap, in case the request was crafted by hand rather than sent by our JS.
 */
function wheellab_sanitize_webhook_value($value, int $max_items = 50) {
    if (is_array($value)) {
        return array_map(
            static fn ($item) => wheellab_sanitize_webhook_value($item, $max_items),
            array_slice($value, 0, $max_items)
        );
    }

    if (is_string($value)) {
        return sanitize_text_field($value);
    }

    if (is_scalar($value) || $value === null) {
        return $value;
    }

    return null;
}
