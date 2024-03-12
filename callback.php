<?php

session_start(); // Start the session

// 1. Retrieve Authorization Code 
$authorizationCode = $_POST['code'];  

// 2. Client and Redirect URI (Match Registered Values)
$clientId = '3c76e42a-e3e6-496c-82a9-e36b4bbd0408'; 
$redirectUri = 'http://localhost:3000/callback'; // Adapt if needed

// 3. Guzzle Setup
require_once 'vendor/autoload.php'; 
$client = new GuzzleHttp\Client();

/*--- 4. Token Request ---*/ 
try {
    $response = $client->request('POST', 'https://fhir.epic.com/interconnect-fhir-oauth/oauth2/token', [
        'form_params' => [
            'grant_type' => 'authorization_code', 
            'code' => $authorizationCode,
            'redirect_uri' => $redirectUri, 
            'client_id' => $clientId 
        ],
        'headers' => [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]
    ]);

    /*--- 5. Handle Success ---*/
    
    // Debugging for success (temporarily output token)
    //echo "<pre>"; 
    //print_r($tokenData); 
    //echo "</pre>";

    if ($response->getStatusCode() == 200) {
        $tokenData = json_decode($response->getBody(), true);
        // Store token in the session
        $_SESSION['access_token'] = $tokenData['access_token'];
        header('Location: /search.php');
        exit;

        // Simplified debugging for login success -  redirect or display a message
        // header('Location: http://localhost:3000/success.html');
        // exit;

    } else {
        /*---  Handle errors gracefully ---*/
        // Log the error
        error_log("Token exchange failed. HTTP status: " . $response->getStatusCode());
        error_log("Response body: " . $response->getBody()->getContents()); 
    
        // Display an error message to the user (redirect or show a message)
        // Example:
        header('Location: /error.php?message=token_exchange_failed'); 
    }

}   catch (GuzzleHttp\Exception\ClientException $e) {
    // Handle Guzzle errors  
    error_log("Guzzle error: " . $e->getMessage());
}