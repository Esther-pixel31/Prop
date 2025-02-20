<?php
// Include database connection
require_once "functions/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tenant_id'])) {
    $tenantId = intval($_POST['tenant_id']); // Get the tenant ID from the AJAX request

    // Query to fetch tenant details along with house and water reading details
    $query = "
        SELECT 
            houses.house_name,
            house_numbers.house_no,
            houses.rent_amount AS rent,
            houses.garbage,
            water_readings.current_reading,
            water_readings.previous_reading,
            water_readings.water_rate,
            water_readings.total_units,
            water_readings.total_amount AS total_consumption,
            invoices.amountDue AS outstanding_balance
        FROM 
            tenants
        JOIN 
            house_numbers ON tenants.houseNumber = house_numbers.id
        JOIN 
            houses ON house_numbers.house_id = houses.houseID
        LEFT JOIN 
            water_readings ON tenants.tenantID = water_readings.tenant_id
        LEFT JOIN 
            invoices ON tenants.tenantID = invoices.tenantID
        WHERE 
            tenants.tenantID = ?;";

    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $tenantId); // Bind tenant ID
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode($data); // Return data as JSON
        } else {
            echo json_encode(['error' => 'No details found for the selected tenant.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error preparing query.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
?>
