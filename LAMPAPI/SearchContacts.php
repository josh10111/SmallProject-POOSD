<?php

include('/var/www/config/config.php'); // config

// receive http request in JSON format
$file = file_get_contents('php://input');
$jsonObj = json_decode($file);

// copy values from received JSON
$userID = $jsonObj->id;
$search = $jsonObj->search;

// connect to database
$conn = new mysqli($DBHOSTNAME, $DBUSERNAME, $DBPASSWORD, $DBNAME);

//check if error connecting
if($conn->connect_error)
{
    errorJSON($conn->connect_error); // send error back
} else // no error connecting
{
    // make a query with error check
    if(!($result = $conn->query("SELECT * FROM Contacts WHERE UserID={$userID} AND (FirstName LIKE '{$search}%' OR LastName LIKE '{$search}%' OR Phone LIKE '{$search}%' OR Email LIKE '{$search}%');")))
    {
        errorJSON($conn->error);
    } else
    {
        // succesful query, need to prepare response now
        if($result->num_rows === 0)
        {
            errorJSON("No Contacts meet search query");
        } else
        {
            validJSON($result, $result->num_rows);
        }
        
    }
}

function validJSON($result, $n) // forms valid JSON
{
    $jsonTemp = '{"results":[';

    // loop through all rows making each element of the results array an object
    for($i = 0; $i < $n; $i++)
    {
        $obj = $result->fetch_object();
        $jsonTemp .= ''. $i .':{"firstName":"'. $obj->FirstName .'","lastName":"'. $obj->LastName .'","phone":"'. $obj->Phone .'","email":"'. $obj->Email .'"}';

        if($i < $n-1)
            $jsonTemp .= ',';
    }
    
    $jsonTemp .= '],"error":""}';
    sendResult($jsonTemp);
}

function errorJSON($error) // forms error JSON
{
    $jsonTemp = '{"results":[],"error":"'. $error .'"}';
    sendResult($jsonTemp);
}

function sendResult($json) // sends response
{
    header("Content-type: application/json");
    echo $json;
}

?>