<?php
    
    // Require the global file for errors
    require_once "db.php";

    // Start sessions
    session_start();

    // If session variable is not set, redirect to login page
    if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
        header("location: login.php");
        exit;
    }

    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tenID'])) {
        $tenantId = intval($_POST['tenID']);
        $tenantName = mysqli_real_escape_string($connection, $_POST['hname'] ?? '');
        $houseName = mysqli_real_escape_string($connection, $_POST['house'] ?? '');

        $email = mysqli_real_escape_string($connection, $_POST['temail'] ?? '');
        $phoneNumber = mysqli_real_escape_string($connection, $_POST['phone'] ?? '');
        $idNumber = mysqli_real_escape_string($connection, $_POST['idnum'] ?? '');
        $profession = mysqli_real_escape_string($connection, $_POST['prof'] ?? '');

        // Add other fields as necessary

        // Update tenant details in the database
        $sql = "UPDATE tenants SET hname='$tenantName', house='$houseName', temail='$email', phone='$phoneNumber', idnum='$idNumber', prof='$profession' WHERE tenantID='$tenantId'";

        if (mysqli_query($connection, $sql)) {
            header("location:../tenants.php?success=1");
        } else {
            $error = "Error updating tenant: " . mysqli_error($connection);
        }
    }

    // Fetch tenant details to pre-fill the form
    if (isset($_GET['tenID'])) {
        $tenantId = intval($_GET['tenID']);
        $sql = "SELECT * FROM tenants WHERE tenantID='$tenantId' LIMIT 1";

        $result = mysqli_query($connection, $sql);
        $tenant = mysqli_fetch_assoc($result);
    }
