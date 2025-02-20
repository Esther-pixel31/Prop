<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

//require the global file for errors
require_once "functions/errors.php";
require_once "functions/db.php";

// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['email']) || empty($_SESSION['email'])){
    header("location: login.php");
    exit;
}

// Check if tenant ID is provided
if (isset($_POST['tenant_id'])) {
    $tenant_id = $_POST['tenant_id'];

    // Fetch the most recent current reading for the selected tenant
    $query = "SELECT current_reading FROM water_readings WHERE tenant_id = ? ORDER BY reading_date DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $stmt->bind_result($last_current_reading);
    $stmt->fetch();
    $stmt->close();

    // Return the result as JSON
    echo json_encode(['last_current_reading' => $last_current_reading]);
} else {
    echo json_encode(['error' => 'No tenant ID provided.']);
}
?>
