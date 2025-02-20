<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "db.php";
require_once "errors.php";

// Initialize the session
session_start();

// If session variable is not set, redirect to login page
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: login.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tenID'])) {
    $tenantId = intval($_POST['tenID']);
    $tenantName = mysqli_real_escape_string($connection, $_POST['tenant_name'] ?? '');

    $email = mysqli_real_escape_string($connection, $_POST['email'] ?? '');
    $phoneNumber = mysqli_real_escape_string($connection, $_POST['phone_number'] ?? '');
    $idNumber = mysqli_real_escape_string($connection, $_POST['id_number'] ?? '');
    $profession = mysqli_real_escape_string($connection, $_POST['profession'] ?? '');

    // Update tenant details in the database
    $sql = "UPDATE tenants SET tenant_name='$tenantName', email='$email', phone_number='$phoneNumber', ID_number='$idNumber', profession='$profession' WHERE tenantID='$tenantId'";

    if (mysqli_query($connection, $sql)) {
        header("location:../tenants.php?success=1");
    } else {
        $error = "Error updating tenant: " . mysqli_error($connection);
    }
} else {
    header("Location:../tenants.php");
    exit;
}
