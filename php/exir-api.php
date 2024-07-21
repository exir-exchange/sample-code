<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

// Replace these with your actual API key and secret
$API_KEY = '';
$API_SECRET = '';

// Base URL for EXIR API
$BASE_URL = 'https://api.exir.io';

// Function to generate the signature
function generateSignature($method, $path, $queryString, $bodyString, $expires)
{
    $message = $method . $path . $queryString . $expires . $bodyString;
    return hash_hmac('sha256', $message, $GLOBALS['API_SECRET']);
}

// Function to send a GET request
function sendGetRequest($path, $headers)
{
    $client = new Client();
    try {
        $response = $client->request('GET', $GLOBALS['BASE_URL'] . $path, [
            'headers' => $headers
        ]);
        return json_decode($response->getBody(), true);
    } catch (GuzzleException $error) {
        echo 'Error getting user balance: ' . $error->getMessage();
    }
}

// Function to send a POST request
function sendPostRequest($path, $headers, $body)
{
    $client = new Client();
    try {
        $response = $client->request('POST', $GLOBALS['BASE_URL'] . $path, [
            'headers' => $headers,
            'json' => $body
        ]);
        return json_decode($response->getBody(), true);
    } catch (GuzzleException $error) {
        echo 'Error creating order: ' . $error->getMessage();
    }
}

// Function to get the user's balance
function getUserBalance()
{
    $method = 'GET';
    $path = '/v2/user/balance';
    $expires = time() + 60; // Expires in 60 seconds
    $queryString = ''; // For GET request
    $bodyString = ''; // For GET request
    $signature = generateSignature($method, $path, $queryString, $bodyString, $expires);

    $headers = [
        'api-key' => $GLOBALS['API_KEY'],
        'api-expires' => $expires,
        'api-signature' => $signature
    ];

    return sendGetRequest($path, $headers);
}

// Function to create an order
function createOrder($symbol, $side, $size, $price, $type)
{
    $method = 'POST';
    $path = '/v2/order';
    $expires = time() + 60; // Expires in 60 seconds
    $body = [
        'symbol' => $symbol,
        'side' => $side,
        'size' => $size,
        'type' => $type,
        'price' => $price
    ];
    $queryString = ''; // For POST request
    $bodyString = json_encode($body);
    $signature = generateSignature($method, $path, $queryString, $bodyString, $expires);

    $headers = [
        'api-key' => $GLOBALS['API_KEY'],
        'api-expires' => $expires,
        'api-signature' => $signature,
        'Content-Type' => 'application/json'
    ];

    return sendPostRequest($path, $headers, $body);
}

// Main function to execute the process
function main()
{
    $balance = getUserBalance();
    echo 'User balance: ' . print_r($balance, true);

    // Replace these values with your desired order parameters
    $symbol = 'btc-usdt';
    $side = 'buy'; // or 'sell'
    $size = 0.01; // Order size in BTC
    $price = 30000; // Order price in USDT
    $type = 'limit';

    $order = createOrder($symbol, $side, $size, $price, $type);
    echo 'Order response: ' . print_r($order, true);
}

main();