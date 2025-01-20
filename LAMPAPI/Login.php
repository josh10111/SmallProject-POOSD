<?php

include('/var/www/config/config.php'); // config

// receive http request in JSON format
$file = file_get_contents('php://input');
$jsonObj = json_decode($file);

// parse for login username and password
$login = $jsonObj->username;
$password = $jsonObj->password;

// connect to database
$conn = new mysqli($DBHOSTNAME, $DBUSERNAME, $DBPASSWORD, $DBNAME);

//check if error connecting
if($conn->connect_error)
{
    errorJSON($conn->connect_error); // return if error
} else // no error
{
    // we are connected to database
    if(!$result = $conn->query("SELECT * FROM Users WHERE Login={$login} AND Password={$password};")) // query to see matching rows
    {
        errorJSON($conn->error);
    } else
    {
        // successful query
        $user = $result->fetch_assoc();
        validJSON($user["ID"], $user["FirstName"], $user["LastName"]);
    }
}

function validJSON($id, $firstName, $lastName) // forms valid JSON
{
    $jsonTemp = '{"id":"'. $id .'","firstName":"'. $firstName .'","lastName":"'. $lastName .'","error":""}';
    sendResult(json_encode($jsonTemp));
}

function errorJSON($error) // forms error JSON
{
    $jsonTemp = '{"id":0,"firstName":"","lastName":"","error":"'. $error .'"}';
    sendResult(json_encode($jsonTemp));
}

function sendResult($jsonResp) // sends response
{
    header("Content-type: application/json");
    echo $jsonResp;
    exit();
}

?>