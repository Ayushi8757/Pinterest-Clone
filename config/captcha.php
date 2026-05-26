<?php

if (!defined('RECAPTCHA_SITE_KEY')) {
    define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY') ?: '6LfXd_csAAAAAD_q3nSXFl4hw_8FWCZ1W9dMbicc');
}

if (!defined('RECAPTCHA_SECRET_KEY')) {
    define('RECAPTCHA_SECRET_KEY', $_ENV['RECAPTCHA_SECRET']);
}

function verify_recaptcha_response(string $token): bool
{
    if ($token === '') {
        return false;
    }

    $payload = http_build_query([
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);

    $responseBody = false;

    if (function_exists('curl_init')) {
        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $responseBody = curl_exec($ch);
        curl_close($ch);
    }

    if ($responseBody === false) {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
                'timeout' => 10,
            ],
        ]);

        $responseBody = @file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify',
            false,
            $context
        );
    }

    if ($responseBody === false) {
        return false;
    }

    $decoded = json_decode($responseBody, true);

    return !empty($decoded['success']);
}
