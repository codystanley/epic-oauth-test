# Epic OAuth Integration Project

## Overview

* **Purpose:** [Briefly describe what your application aims to do]
* **Technologies:** 
    * PHP ([Specify version if relevant])
    * MySQL (or your chosen database)
    * JavaScript 
    * Guzzle HTTP Client
* **Current State:** [Implemented features vs.  in development]

## Architecture and Workflow

* **Authorization Flow:**
    1. EPIC Test User Login
        - FHIRTWO
        - EpicFhir11!
    [Describe how the user is redirected to Epic's authorization page]
    2. [Explain the exchange of the authorization code for tokens]
    3. [Indicate how access and refresh tokens are stored]
* **Search Logic**
    * [List supported search methods - MRN, DOB/Name]
    * [Outline how API requests are constructed with Guzzle]
    * [Describe result processing and how they are displayed]

## Setup and Configuration

1. **Prerequisites:**
    * ...
2. **Database Setup**
    * **SQL Script:**

    ```sql
    CREATE TABLE oauth_credentials (
        -- ... your table structure here ...
    );  
    ```
3. **Credentials**
    * [Development: Explain the use of AWS Secrets Manager]
    * [Production: Outline your planned encryption approach] 

## API Interaction

* **Epic Endpoints:**
    * `/oauth2/token`
    * `/interconnect-fhir-oauth/api/FHIR/R4/Patient` 
* **Search Parameters:**
    * `identifier` (MRN)
    * `birthdate`, `family`, `given`

## Development Roadmap 

* **Planned Features:** ...
* **Encryption Implementation:** ...
* **Potential Enhancements:** ...

## Contributing

* [If applicable, add instructions for others on how to contribute to your project] 

# Test Patients
|Patient|MRN|DOB|Gender|
|------------|------|----------|------|
|Camila Lopez|203713|1987-09-12|female|
|Derrick Lin|203711|1973-06-03|male|
|Desiree Powell|203714|2014-11-14|female|
|Elijah Davis|203709|1993-08-18|male|
|Linda Ross|203712|1969-04-27|female|
|Olivia Roberts|203715|2003-01-07|female|
|Warren McGinnis|203710|1952-05-24|male|