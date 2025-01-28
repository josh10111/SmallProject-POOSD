<?php
	
	include('/var/www/config/config.php');
	session_start();
	$_SESSION["ID"] = 1; //for test purpose.
	if (!isset($_SESSION["ID"])) 
	{
    		returnWithError("User is not logged in.");
    		exit();
	}	

	$inData = getRequestInfo();

	$userID = isset($inData["userID"]) ? $inData["userID"] : $_SESSION["userID"];
	$contactID =$inData["contactID"];
	$firstname = $inData["firstName"];
	$lastname = $inData["lastName"];
	$phone = $inData["phone"];
	$email = $inData["email"];
	if (empty($firstname) || empty($lastname) || empty($phone)|| empty($email) || empty($contactID)) 
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
		$stmt = $conn->prepare("SELECT ID FROM Contacts WHERE ID = ?");
		$stmt->bind_param("i", $contactID);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows === 0) 
		{
			returnWithError("Contact ID not found.");
			$stmt->close();
			$conn->close();
			exit();
		}
		$stmt = $conn->prepare("UPDATE Contacts SET FirstName = ?, LastName = ?, Phone = ?, Email = ? WHERE ID = ?");
		$stmt->bind_param("ssssi", $firstname, $lastname, $phone, $email, $contactID);
		if($stmt->execute())
		{
			returnWithSuccess("Contact Updated Successfully",$contactID, $firstname, $lastname, $phone, $email);
		}
		else 
		{
			returnWithError("Failed to update contact".$stmt->error);
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
		$retValue = '{"Error":"","contactID":0,"firstName":"","lastName":"","phone":"","email":"","error":"' . $err . '"}';
		sendResultInfoAsJson($retValue);
	}
	function returnWithSuccess($message, $contactID, $firstname, $lastname, $phone, $email) 
	{
		$retValue = '{"success":"' . $message . '", "contactID":' . $contactID . ',"firstName":"' . $firstname . '","lastName":"' . $lastname . '","phone":"' . $phone . '","email":"' . $email . '","error":""}';
		sendResultInfoAsJson($retValue);
	}

	
?>
