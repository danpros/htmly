<?php
if (!defined('HTMLY')) die('HTMLy Direct Access Denied');

/**
 * Dispatch an asynchronous Webhook HTTP POST event to registered endpoints (e.g. n8n workflows)
 * 
 * @param string $event Event name (e.g. 'post_published', 'post_deleted')
 * @param array $payload Event payload data
 */
function dispatch_webhook_event($event, array $payload)
{
    $configFile = 'config/webhooks.ini';
    if (!file_exists($configFile)) {
        return;
    }

    $config = parse_ini_file($configFile, true);
    $webhooks = $config['webhooks'] ?? array();

    $targetUrl = $webhooks[$event] ?? ($webhooks['all'] ?? null);
    if (empty($targetUrl)) {
        return;
    }

    $data = array(
        'event' => $event,
        'timestamp' => date('c'),
        'payload' => $payload
    );

    $jsonPayload = json_encode($data);

    // Non-blocking cURL / Stream Context POST
    if (function_exists('curl_init')) {
        $ch = curl_init($targetUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonPayload),
            'X-HTMLy-Event: ' . $event
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // 3 seconds timeout
        curl_exec($ch);
        curl_close($ch);
    } else {
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n" .
                            "X-HTMLy-Event: " . $event . "\r\n",
                'content' => $jsonPayload,
                'timeout' => 3
            )
        );
        $context = stream_context_create($opts);
        @file_get_contents($targetUrl, false, $context);
    }
}
