<?php
require_once 'vendor/autoload.php';

session_start();

if (isset($_SESSION['access_token'])) {
    // Render the search form
?>

<div id="searchSection"> 
    <h2>Patient Search</h2>
    <select id="searchMethod">
        <option value="">Select Search Method</option>
        <option value="mrn">Search by MRN</option>
        <option value="dob">Search by Date of Birth, Last Name and First Name</option>
    </select>

    <div id="mrnSearchForm" style="display: none;"> 
        <form id="patientSearchForm" action="" method="GET"> 
            <div class="mb-3">
                <label for="identifier" class="form-label">Identifier (MRN)</label>
                <input type="text" class="form-control" id="identifier" name="identifier" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div><!-- #mrnSearchForm -->

    <div id="dobSearchForm" style="display: none;"> 
        <form id="patientSearchForm" action="" method="GET"> 
            <div class="mb-3">
                <label for="birthdate" class="form-label">Date of Birth:</label>
                <input type="date" class="form-control" id="birthdate" name="birthdate" required>
            </div>
            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name:</label>
                <input type="text" class="form-control" id="lastName" name="lastName" required>
            </div>
            <div class="mb-3">
                <label for="firstName" class="form-label">First Name:</label>
                <input type="text" class="form-control" id="firstName" name="firstName" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div><!-- #dobSearchForm -->

    <div id="searchResults">
    </div>
</div><!-- #searchSection -->

<script src="js/search.js"></script>

<?php

// Patient Search Logic (When the form is submitted)
function performPatientSearch($searchParams) { // A function to handle searches with varied parameters
    $baseUrl = 'https://fhir.epic.com/interconnect-fhir-oauth/api/FHIR/R4/Patient';  
    $client = new GuzzleHttp\Client();

    try {
        $response = $client->request('GET', $baseUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $_SESSION['access_token'],
                'Accept' => "application/json+fhir"
            ],
            'query' => $searchParams
        ]);

        if ($response->getStatusCode() == 200) {
            $searchResults = json_decode($response->getBody(), true);

            // Display the search results (basic example - you'll enhance this)
            // Display the search results 
            echo "<h2>Search Results:</h2>";

            if ($searchResults['total'] > 0) {
                $patient = $searchResults['entry'][0]['resource'];

                echo "<table>";
                echo "<tr><th>MRN</th><td>" . $patient['identifier'][5]['value'] . "</td></tr>";
                echo "<tr><th>Last Name</th><td>" . $patient['name'][0]['family'] . "</td></tr>";
                echo "<tr><th>First Name</th><td>" . $patient['name'][0]['given'][0] . "</td></tr>";
                echo "<tr><th>Date of Birth</th><td>" . $patient['birthDate'] . "</td></tr>";
                echo "<tr><th>Gender</th><td>" . $patient['gender'] . "</td></tr>";
                echo "<tr><th>State</th><td>" . $patient['address'][0]['state'] . "</td></tr>";
                echo "</table>";

            } else { // Handle no returned patients.
                echo "<p>No patients found.</p>";
            }

        } else { // Handle API request error (more specific error display later)
            echo "<p>Search failed. Please contact support.</p>"; 
        }

    } catch (GuzzleHttp\Exception\ClientException $e) {
        // Check if token expired by looking for 401 Unauthorized
        if ($e->getResponse() && $e->getResponse()->getStatusCode() === 401) {
            // Token likely expired - Redirect to login
            header('Location: /index.html');
            exit; 
        } else {
            // Handle other Guzzle errors  
            echo "<p>An error occurred. Please contact support.</p>";
        }
    }
}
    // Search by MRN
    if (isset($_GET['identifier'])) {
        $identifier = $_GET['identifier'];
        $searchParams = ['identifier' => $identifier];
        performPatientSearch($searchParams); 
    } 

    //Search by DOB, Last and First
    elseif (isset($_GET['birthdate']) && isset($_GET['lastName']) && isset($_GET['firstName'])) {
        $birthdate = $_GET['birthdate'];
        $lastName = $_GET['lastName'];
        $firstName = $_GET['firstName'];
        $searchParams = [
            'given' => $firstName,  
            'family' => $lastName,
            'birthdate' => $birthdate
        ];
        performPatientSearch($searchParams);
    }

    //  Handle the case where no valid search parameters are present
    else {
        echo "<p>Please select a search method and provide valid search parameters.</p>";
    }

} else {
    // User is not logged in
    header('Location: /index.html'); // or your login page
    exit;
}
