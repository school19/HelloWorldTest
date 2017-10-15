<?php
/**
 * Created by PhpStorm.
 * User: Chaz
 * Date: 10/13/2017
 * Time: 9:20 PM
 */

//Trimming function to pass to array_filter
function trim_value(&$value)
{
    $value = trim($value);
}

//trim whitespace off of $_POST
array_filter($_POST, 'trim_value');

$filtered_input = array(
    'first_name' => filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING),
    'last_name' => filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING),
    'address1' => filter_input(INPUT_POST, 'address1', FILTER_SANITIZE_STRING),
    'address2' => filter_input(INPUT_POST, 'address2', FILTER_SANITIZE_STRING),
    'city' => filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING),
    'state' => filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING),
    'country' => filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING),

    //Using FILTER_SANITIZE_STRING instead of FILTER_SANITIZE_NUMBER because a 9 digit zip_code is sent with the "-" character as well
    'zip_code' => filter_input(INPUT_POST, 'zip_code', FILTER_SANITIZE_STRING),
);

//address2 is sent as "undefined" if it's empty, but we'll also check for the empty string too
if($filtered_input['address2'] === 'undefined' || $filtered_input['address2'] === '')
{
    //Set to null for a more descriptive sql record
    $filtered_input['address2'] = null;
}

//Using environment variables keeps configuration information out of the source tree
$db_host = getenv('DB_HOST');
$db_pass = getenv('DB_PASS');
$db_user = getenv('DB_USER');
$db_database = getenv('DB_DATABASENAME');
$db_port = getenv('DB_PORT');

$database_connection = new mysqli($db_host, $db_user, $db_pass, $db_database, $db_port);

$insert_stmt = $database_connection->prepare(
    "INSERT INTO registrants (first_name, last_name, address1, address2, city, state, zip_code, country, registration_time)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW());");

if(!$insert_stmt)
{
    http_response_code(500);
    echo "Failed to prepare statement: (" . $database_connection->errno . ") " . $database_connection->error;
    exit(1);
}

$result = $insert_stmt->bind_param("ssssssss", $filtered_input['first_name'], $filtered_input['last_name'],
    $filtered_input['address1'], $filtered_input['address2'], $filtered_input['city'],
    $filtered_input['state'], $filtered_input['zip_code'], $filtered_input['country']);

if(!$result)
{
    http_response_code(500);
    echo "Failed to bind data params: (" . $database_connection->errno . ") " . $database_connection->error;
    exit(1);
}

if(!$insert_stmt->execute())
{
    http_response_code(500);
    echo "Failed to insert record into database: (" .$database_connection->errno .") " .$database_connection->error;
    exit(1);
}

//send the user to the registration page.
header("Location: /confirmation.html");
die();