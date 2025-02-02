<?php

include('/var/www/config/config.php'); // config

// receive http request in JSON format
$file = file_get_contents('php://input');
$jsonObj = json_decode($file);

// copy values from received JSON
$login = $jsonObj->login;
$password = $jsonObj->password;

// connect to database
$conn = new mysqli($DBHOSTNAME, $DBUSERNAME, $DBPASSWORD, $DBNAME);

//check if error connecting
if($conn->connect_error)
{
    errorJSON($conn->connect_error); // send error back
} else // no error
{
    // we are connected to database
    // make query
    if(!($result = $conn->query("SELECT * FROM Users WHERE Login=\"{$login}\" AND Password=\"{$password}\";"))) // if error during query
    {
        errorJSON($conn->error);
    } else // no error
    {
        // fetch result into object
        if(!($user = $result->fetch_object())) // if no object
        {
            errorJSON("No User Found");
        } else
        {
            // successful query
            setCookies($user->ID, $user->FirstName, $user->LastName);
            validJSON($user->ID, $user->FirstName, $user->LastName);
        }
    }
}

function setCookies($id, $firstName, $lastName) // sets cookies
{
    $expireTime = time() + (20 * 60); // 20 minutes from now
    setcookie("userId", $id, $expireTime, "/");
    setcookie("firstName", $firstName, $expireTime, "/");
    setcookie("lastName", $lastName, $expireTime, "/");
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