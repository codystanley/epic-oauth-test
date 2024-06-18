
document.getElementById('initiateEpicLogin').addEventListener('click', initiateEpicLogin);

function initiateEpicLogin() {
    const clientId = '3c76e42a-e3e6-496c-82a9-e36b4bbd0408'; // Must match an entry in https://fhir.epic.com/Developer/Apps
    const authorizationBaseUrl = 'https://fhir.epic.com/interconnect-fhir-oauth/oauth2/authorize';
    const redirectUri = 'http://localhost:3000/callback'; // Must match an entry in https://fhir.epic.com/Developer/Apps
    const scopes = 'openid';
    const audience = 'https://fhir.epic.com/interconnect-fhir-oauth/api/FHIR/R4/'; // /DSTU2/ or /STU3/ or /R4/
    const state = 'abc123'; // Temporary placeholder. Should be ramdomly generated each call.

    // Contruct the Authorization URL
    const authorizationUrl = `${authorizationBaseUrl}?response_type=code&redirect_uri=${encodeURIComponent(redirectUri)}&client_id=${clientId}&scope=${scopes}&aud=${encodeURIComponent(audience)}&state=${state}`; 
    
    // Direct user for Epic authorization
    window.location.href = authorizationUrl;
}

/**
 * After logging in, the user is redirected to redirctUri and a 'code' is passed in the URL.
 * This 'code' is then exchanged for an OAuth token in callback.php
**/

// Code to handle the redirect with the authorization code
const urlParams = new URLSearchParams(window.location.search);
const authorizationCode = urlParams.get('code'); 

if (authorizationCode) { 
    // Update hidden form with code and submit
    document.getElementById('codeForm').elements['code'].value = authorizationCode;
    document.getElementById('codeForm').submit();
}

/**
 * After attaining the token, expose the Patient Search function
 **/

// Patient Search Functionality
const patientSearchForm = document.getElementById('patientSearchForm');
const searchResultsDiv = document.getElementById('searchResults');

patientSearchForm.addEventListener('submit', (event) => {
    fetch('/callback', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify({ birthdate, lastName, firstName })
    })

    .then(response => {
        searchResultsDiv.innerHTML = ''; // Clear previous results 

    // Process and display search results here ...
    if (searchResults.length === 0) {
        // ... (your code for no results) ...
    } else {
        const resultsList = document.createElement('ul'); // Create a list
        searchResults.forEach(patient => {
            const listItem = document.createElement('li');
            listItem.textContent = `${patient.firstName} ${patient.lastName} (ID: ${patient.id}) - Birthdate: ${patient.birthDate}`;
            resultsList.appendChild(listItem);
        });
        searchResultsDiv.appendChild(resultsList); 
    }
    })
});
