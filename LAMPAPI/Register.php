<?php

include('/var/www/config/config.php'); // config

// receive http request in JSON format
$file = file_get_contents('php://input');
$jsonObj = json_decode($file);

// copy values from received JSON
$firstName = $jsonObj->firstName;
$lastName = $jsonObj->lastName;
$username = $jsonObj->username;
$password = $jsonObj->password;

// connect to database
$conn = new mysqli($DBHOSTNAME, $DBUSERNAME, $DBPASSWORD, $DBNAME);

//check if error connecting
if($conn->connect_error)
{
    errorJSON($conn->connect_error); // send error back
} else // no error connecting
{
    // make a query with error check
    if(!($result = $conn->query("SELECT * FROM Users WHERE Login=\"{$username}\";")))
    {
        errorJSON($conn->error);
    } else // success
    {
        if($result->num_rows > 0) // if data containing username exists
        {
            errorJSON("Username taken!"); // return username taken
        } else // username not taken
        {
            // insert into database
            if(!($result = $conn->query("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (\"{$firstName}\", \"{$lastName}\", \"{$username}\", \"{$password}\");")))
            {
                errorJSON($conn->error); // error inserting
            } else
            {
                validJSON($conn->insert_id, $firstName, $lastName); // inserted
            }
        }
    }
}

function validJSON($id, $firstName, $lastName) // forms valid JSON
{
    $jsonTemp = '{"id":'. $id .',"firstName":"'. $firstName .'","lastName":"'. $lastName .'","error":""}';
    sendResult($jsonTemp);
}

function errorJSON($error) // forms error JSON
{
    $jsonTemp = '{"id":0,"firstName":"","lastName":"","error":"'. $error .'"}';
    sendResult($jsonTemp);
}

function sendResult($jsonResp) // sends response
{
    header("Content-type: application/json");
    echo $jsonResp;
    exit();
}

?>