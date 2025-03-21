<?php
require_once "functions/db.php";

if (isset($_GET['tenant_id'])) {
    $tenantID = intval($_GET['tenant_id']);

    $query = "SELECT outstanding_balance FROM tenantsdetailsview WHERE tenantID = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $tenantID);
        $stmt->execute();
        $stmt->bind_result($balance);
        $stmt->fetch();
        $stmt->close();
        
        echo json_encode(['balance' => $balance]);
    } else {
        echo json_encode(['balance' => 0]); // Return 0 if query fails
    }
} else {
    echo json_encode(['balance' => 0]); // Return 0 if no tenant_id is provided
}
?>
