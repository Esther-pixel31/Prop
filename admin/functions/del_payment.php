<?php
// Start session and check authentication
session_start();
if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: ../login.php");
    exit;
}

// Include database connection
require_once "db.php";

// Check if form was submitted
if(isset($_POST['deleteTenant'])) {
    // Get and sanitize payment ID
    $paymentID = mysqli_real_escape_string($connection, $_POST['tenID']);

    // Delete payment record
    $sql = "DELETE FROM payments WHERE paymentID = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $paymentID);
    
    if(mysqli_stmt_execute($stmt)) {
        // Success - redirect with success message
        header("location: ../payments.php?deleted=1");
    } else {
        // Error - redirect with error message
        header("location: ../payments.php?del_error=1");
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    exit;
} else {
    // If form wasn't submitted, redirect to payments page
    header("location: ../payments.php");
    exit;
}
?>
