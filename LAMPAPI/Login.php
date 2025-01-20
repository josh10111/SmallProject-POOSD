<?php

// This is an API endpoint for logging into the Contact Manger Website
// We will receive a request in JSON format
// Based on the information in the JSON, we will need to make a query on the database to retrieve the needed information
// In this case it is the User's information

// receive request
//receive JSON
$file = file_get_contents('php://input');
$jsonObj = json_decode($file);

// parse for login username and password
$login = $jsonObj->username;
$password = $jsonObj->password;

// connect to database and prepare the SQL statement
$dbusername = "";
$dbpassword = "";
$database = "";

$conn = new mysqli('localhost', $dbusername, $dbpassword, $database);

//check if error connected
if($conn->connect_error)
{
    // need to return error
    errorJSON($conn->connect_error);
} else
{
    // no error
    // we are connected to database
    // make sure to fill template with username and password


    // check if query retrieved a user or not
        // if not return error message
        // if it did we need to return the user in JSON format
            // which means we need to convert the data into JSON format
}

function validJSON()
{

}

function errorJSON($error)
{
    $jsonTemp = '{"id":0,"firstName":"","lastName":"","error":"'.$error.'"}';
    sendResult(json_encode($jsonTemp));
}

function sendResult($jsonResp)
{
    header("Content-type: application/json");
    echo $jsonResp;
    exit();
}

?>