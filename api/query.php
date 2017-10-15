<?php
/**
 * Created by PhpStorm.
 * User: Chaz
 * Date: 10/13/2017
 * Time: 9:20 PM
 */

//<insert some form of user auth here beyond the scope of what this assessment asked for>


//Using environment variables keeps configuration information out of the source tree
$db_host = getenv('DB_HOST');
$db_pass = getenv('DB_PASS');
$db_user = getenv('DB_USER');
$db_database = getenv('DB_DATABASENAME');
$db_port = getenv('DB_PORT');

$database_connection = new mysqli($db_host, $db_user, $db_pass, $db_database, $db_port);
if(!$database_connection)
{
    http_response_code(500);
    echo "Failed to open database connection: (" . $database_connection->errno . ") " . $database_connection->error;
    exit(1);
}

//Using parameter names in the event that additional columns are added that we don't care about here
// and we don't need a prepared statement here because we have no user input coming in.
$query_result = $database_connection->query("SELECT first_name, last_name, address1, address2, city, state, zip_code, country, registration_time FROM registrants ORDER BY registration_time DESC");

if(!$query_result)
{
    http_response_code(500);
    echo "Database query failed: (" .$database_connection->errno .") " . $database_connection->error;
}
$result_array = $query_result->fetch_all();

echo json_encode($result_array);