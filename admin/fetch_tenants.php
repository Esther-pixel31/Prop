<?php
require_once "functions/db.php";

$month = $_POST['month'];
$year = $_POST['year'];

$query = "SELECT t.tenantID, t.tenant_name 
          FROM tenants t 
          WHERE NOT EXISTS (
              SELECT 1 FROM water_readings w 
              WHERE w.tenant_id = t.tenantID 
              AND MONTH(w.reading_date) = ? 
              AND YEAR(w.reading_date) = ?
          )";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

$tenants = [];
while ($row = $result->fetch_assoc()) {
    $tenants[] = $row;
}

echo json_encode($tenants);
?>
