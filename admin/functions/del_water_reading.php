<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once "db.php";

// Check if the id is set
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Prepare the delete query
$sql = "DELETE FROM `water_readings` WHERE id = ?";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: ../water.php?deleted=true");
    } else {
        // Redirect with error message
        header("Location: ../water.php?del_error=true");
    }

    $stmt->close();
} else {
    // Redirect if no id is provided
    header("Location: ../water.php?del_error=true");
}
?>
