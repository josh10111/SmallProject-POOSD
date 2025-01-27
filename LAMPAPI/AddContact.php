<?php

	include('/var/www/config/config.php');
	session_start();
	$_SESSION["userID"] = 1; //for test purpose.
	if (!isset($_SESSION["userID"])) 
	{
    		returnWithError("User is not logged in.");
    		exit();
	}

	$inData = getRequestInfo();

	$firstname = $inData["firstName"];
	$lastname = $inData["lastName"];
	$phone = $inData["phone"];
	$email = $inData["email"];
	$userID = isset($inData["userID"]) ? $inData["userID"] : $_SESSION["userID"];

	if (empty($userID) || empty($firstname) || empty($lastname) || empty($phone)|| empty($email)) 
	{
    		returnWithError("Missing required fields.");
   		exit();
	}
	$conn = new mysqli($DBHOSTNAME, $DBUSERNAME, $DBPASSWORD, $DBNAME);
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("insert into Contacts (FirstName,LastName,Phone,Email,UserID) VALUES(?,?,?,?,?)");
		$stmt->bind_param("ssssi", $firstname, $lastname, $phone, $email, $userID);
		if($stmt->execute())
		{
			$contactId = $stmt->insert_id;
			validJSON($contactId, $firstname, $lastname, $phone, $email);
		}
		else 
		{
			returnWithError("Failed to add contact".$stmt->error);
		}
		$stmt->close();
		$conn->close();
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson($retValue);
	}
	
	function returnWithSuccess($message) 
	{

		$retValue = '{"success":"' . $message . '"}';
    		sendResultInfoAsJson($retValue);
	}
	function validJSON($id, $firstName, $lastName, $phone, $email)
	{
    		$jsonTemp = '{"contactID":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","phone":"' . $phone . '","email":"' . $email . '","error":""}';
    		sendResultInfoAsJson($jsonTemp);
	}

	
?>
