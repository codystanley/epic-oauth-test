<?php

/* ------------------------------------------------------------------------------
    This file handes the redirect after the user logs in to Epic from index.html.
    It retrieves the authorization code and exchanges it for an OAuth2 token.
------------------------------------------------------------------------------ */

require_once 'vendor/autoload.php'; // Guzzle loader. Guzzle makes it easier to handle HTTP requests in PHP.
require_once 'config.php';

$config = require 'config.php';

/**
 * Create a new mysqli object and establish a connection to the database.
 * The database credentials are stored in the config.php file.
 */
$db = new mysqli(
    $config['database']['host'], 
    $config['database']['username'], 
    $config['database']['password'], 
    $config['database']['database_name']
);

/* 1. Fetch Client Credentials from DB*/
$stmt = $db->prepare("SELECT client_id, state, scope, redirect_uri, token_endpoint FROM epic_clients WHERE id = 1"); // No need for bind_param here
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $clientId = $row['client_id'];
    $clientSecret = $row['client_secret'];
    $tokenEndpoint = $row['token_endpoint'];
}

session_start(); // Start the session

/* 2. Retrieve Authorization Code */
$authorizationCode = $_POST['code'];  

/* 3. Start Guzzle */
$client = new GuzzleHttp\Client();

/* 4. Token Request */ 
try {
    $response = $client->request('POST', $tokenEndpoint, [
        'form_params' => [
            'grant_type' => 'authorization_code', 
            'code' => $authorizationCode,
            'redirect_uri' => $config['redirect_uri'], // Load from config 
            'client_id' => $clientId  // Load from database
        ],
        'headers' => [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]
    ]);

    /* 4a. Handle Success */

    if ($response->getStatusCode() == 200) {
        $tokenData = json_decode($response->getBody(), true);
        // Store token in the session
        $_SESSION['access_token'] = $tokenData['access_token'];
        // Load the search application - And we're off!
        header('Location: /search.php');
        exit;

        # Debugging #
        #############
        //echo "<pre>"; 
        //print_r($tokenData); 
        //echo "</pre>";

    } else {
        /* 4b  Handle token exchange errors gracefully */

        // Log the error
        error_log("Token exchange failed. HTTP status: " . $response->getStatusCode());
        error_log("Response body: " . $response->getBody()->getContents()); 
    
        // Display an error message to the user (redirect or show a message)
        header('Location: /error.php?message=token_exchange_failed');
    }

/* 5. Handle Guzzle Errors */
}   catch (GuzzleHttp\Exception\ClientException $e) { 
    // Log the error
    error_log("Guzzle error: " . $e->getMessage());
}