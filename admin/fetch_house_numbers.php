<?php
require_once "functions/db.php";

if (isset($_GET['house_id'])) {
    $house_id = intval($_GET['house_id']);

    // Query to get available house numbers (excluding assigned ones)
    $query = "SELECT id, house_no FROM house_numbers WHERE house_id = ? AND id NOT IN (SELECT houseNumber FROM tenants)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $house_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    echo "<option value=''>**Select a house number**</option>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . $row['id'] . "'>" . $row['house_no'] . "</option>";
    }
}
?>
