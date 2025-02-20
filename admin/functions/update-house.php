<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "db.php";
require_once "errors.php";

// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['email']) || empty($_SESSION['email'])){
  header("location: login.php");
  exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $house_id = intval($_POST['id']);
    $house_name = mysqli_real_escape_string($connection, $_POST['house_name']);
    $house_no = mysqli_real_escape_string($connection, $_POST['house_no']);

    // Update the house details in the database
    $sql = "UPDATE house_numbers SET house_no = '$house_no' WHERE id = $house_id";
    $result = mysqli_query($connection, $sql);

    if ($result) {
        header("Location:../house-no.php?success=true");
        exit;
    } else {
        echo '<div class="alert alert-danger">Error updating house details. Please try again.</div>';
    }
}

// Fetch the current house details for the form
if (isset($_GET['id'])) {
    $house_id = intval($_GET['id']);
    $sql = "SELECT house_name, house_no FROM house_numbers WHERE id = $house_id";
    $query = mysqli_query($connection, $sql);
    
    if ($query) {
        $house = mysqli_fetch_assoc($query);
    } else {
        echo '<div class="alert alert-danger">Error fetching house details. Please try again.</div>';
        exit;
    }
} else {
    header("Location:../house-no.php");
    exit;
}
?>


