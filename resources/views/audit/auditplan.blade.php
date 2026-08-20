<?php

// use Illuminate\Support\Facades\Http;

// $response = Http::post('https://tmegov.onex-aura.com/api/sms', [
//     'auth_key' => '8uWr4sBj', // Store key in .env
//     'to'       => '8148958988',
//     'message'  => 'Dear User, your password reset request has been received. Use OTP 1234 to reset your password. This OTP is valid for 5 minutes. - CAMS',
//     'sender'   => 'DGCAMS',
// ]);

// $data = $response->json();
// dd($data); // Debugging response
$url = 'https://tmegov.onex-aura.com/api/sms';
$data = [
    'key' => env('SMS_AUTH_KEY'), // API key from .env
    'from' => 'DGCAMS', // Sender ID
    'to' => '8148958988', // Recipient's phone number
    'body' => 'Your OTP is 1234', // SMS content
    'template_id' => '1007758064057327120', // Add Template ID here
    'entityid' => '1001227948943862859', // Add Template ID here
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

dd(json_decode($response, true));

die();

// $response = Http::withHeaders([
//     'Authorization' => 'Bearer ' . env('SMS_AUTH_KEY'), // Some APIs use Bearer token
// ])->post('https://tmegov.onex-aura.com/api/sms', [
//     'to' => '8148958988',
//     'message' => 'test OTP 1234',
//     'sender' => 'DGCAMS',
// ]);

// dd($response->json()); // Debug response

// die();

function sendSMS()
{
    $api_url = 'https://tmegov.onex-aura.com/api/sms'; // Replace with your SMS API URL
    $api_key = '8uWr4sBj'; // Replace with your API key
    $sender_id = 'DGCAMS'; // Optional, if required by API

    // Prepare POST data
    $data = [
        'key' => $api_key,
        'from' => $sender_id,
        'to' => '8148958988',
        'body' => 'Dear User, your password reset request has been received. Use OTP 1234 to reset your password. This OTP is valid for 5 minutes. - CAMS',
        'entityid' => '1001227948943862859',
        'templateid' => '1007758064057327120',
    ];

    // Initialize cURL
    $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Ensure response is returned
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verification (optional)
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout

$response = curl_exec($ch);

print_r($response);
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch); // Show error if cURL fails
}

curl_close($ch);

dd(json_decode($response, true));

die;

    // Debugging the response
    if ($http_code == 200) {
        $decoded_response = json_decode($response, true);
        if ($decoded_response) {
            return $decoded_response; // Return the decoded response
        } else {
            return ['status' => 'error', 'message' => 'Invalid JSON response', 'raw' => $response];
        }
    } else {
        return ['status' => 'error', 'message' => "HTTP Code: $http_code", 'error' => $error];
    }

    // return $response;
}

$response = sendSMS();

print_r($response);
