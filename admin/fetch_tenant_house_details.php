<?php
// Include database connection
require_once "functions/db.php";

if (isset($_POST['tenant_id'])) {
    $tenant_id = intval($_POST['tenant_id']); // Get tenant ID securely

    // Updated SQL query to fetch tenant's house details
    $query = "SELECT 
        tenants.tenant_name, 
        houses.house_name, 
        house_numbers.house_no
    FROM 
        tenants 
    LEFT JOIN 
        house_numbers ON tenants.houseNumber = house_numbers.id
    LEFT JOIN 
        houses ON house_numbers.house_id = houses.houseID
    WHERE 
        tenants.tenantID = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $house_data = $result->fetch_assoc();
        echo json_encode($house_data); // Send data back as JSON
    } else {
        echo json_encode(['error' => 'No data found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
