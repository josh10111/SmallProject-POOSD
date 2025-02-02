<?php
    include('/var/www/config/config.php');
    session_start();

    // For testing purposes: mock user session
    $_SESSION["ID"] = 1;

    // Check if user is logged in
    if (!isset($_SESSION["ID"])) {
        returnWithError("User is not logged in.");
        exit();
    }

    // Get the request data
    $inData = getRequestInfo();

    $contactID = $inData["contactID"];

    // Check if contactID is provided
    if (empty($contactID)) {
        returnWithError("Missing required field: contactID.");
        exit();
    }

    // Connect to the database
    $conn = new mysqli($DBHOSTNAME, $DBUSERNAME, $DBPASSWORD, $DBNAME);

    if ($conn->connect_error) {
        returnWithError("Database connection failed: " . $conn->connect_error);
        exit();
    }

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM Contacts WHERE ID = ?");
    $stmt->bind_param("i", $contactID);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            returnWithSuccess("Contact deleted successfully.", $contactID);
        } else {
            returnWithError("Contact not found or already deleted.");
        }
    } else {
        returnWithError("Failed to delete contact: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    // Helper functions
    function getRequestInfo() {
        return json_decode(file_get_contents('php://input'), true);
    }

    function sendResultInfoAsJson($obj) {
        header('Content-type: application/json');
        echo $obj;
    }

    function returnWithError($err) {
        $retValue = '{"success":"","contactID":0,"error":"' . $err . '"}';
        sendResultInfoAsJson($retValue);
    }

    function returnWithSuccess($message, $contactID) {
        $retValue = '{"success":"' . $message . '","contactID":' . $contactID . ',"error":""}';
        sendResultInfoAsJson($retValue);
    }
?>
