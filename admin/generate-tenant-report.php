<?php
require_once 'functions/db.php';
require_once 'functions/errors.php';
require_once 'functions/tcpdf/tcpdf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenantId = $_POST['tenant_id'];

    // Fetch tenant, house, invoice, payment, and water readings details
    $query = "
        SELECT 
            invoices.invoiceNumber, 
            invoices.dateOfInvoice, 
            invoices.dateDue, 
            tenants.tenant_name, 
            houses.house_name, 
            house_numbers.house_no, 
            houses.rent_amount AS rent, 
            houses.garbage AS garbage, 
            water_readings.previous_reading, 
            water_readings.current_reading, 
            water_readings.total_units, 
            water_readings.water_rate, 
            water_readings.total_amount AS total_consumption, 
            invoices.amountDue AS outstanding_balance, 
            invoices.totalAmount AS total_amount 
        FROM 
            invoices 
        LEFT JOIN tenants ON invoices.tenantID = tenants.tenantID 
        LEFT JOIN house_numbers ON tenants.houseNumber = house_numbers.id 
        LEFT JOIN houses ON house_numbers.house_id = houses.houseID 
        LEFT JOIN water_readings ON tenants.tenantID = water_readings.tenant_id 
        WHERE tenants.tenantID = ?
        GROUP BY invoices.invoiceNumber";

    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $tenantId);
    $stmt->execute();
    $result = $stmt->get_result();
    $tenantData = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($tenantData)) {
        die("No data found for this tenant.");
    }

    // Fetch payment history
    $paymentsQuery = "
        SELECT dateofPayment, amountPaid, mpesaCode, comment
        FROM payments
        WHERE tenantID = ?
        ORDER BY dateofPayment DESC";

    $stmt = $connection->prepare($paymentsQuery);
    $stmt->bind_param("i", $tenantId);
    $stmt->execute();
    $paymentsResult = $stmt->get_result();
    $payments = $paymentsResult->fetch_all(MYSQLI_ASSOC);

    // Calculate total payments
    $totalPayments = array_sum(array_column($payments, 'amountPaid'));

    // Generate PDF report
    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); // 'L' for Landscape
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Nyumbani Homes');
    $pdf->SetTitle('Tenant Report - ' . $tenantData[0]['tenant_name']);
    $pdf->AddPage();

    // Report Header
    $html = "<h1>Tenant Report: {$tenantData[0]['tenant_name']}</h1>";
    $html .= "<p><strong>Property Name:</strong> {$tenantData[0]['house_name']}</p>";
    $html .= "<p><strong>House Number:</strong> {$tenantData[0]['house_no']}</p>";
    $html .= "<p><strong>Monthly Rent:</strong> KES {$tenantData[0]['rent']}</p>";
    $html .= "<p><strong>Garbage Fee:</strong> KES {$tenantData[0]['garbage']}</p>";

    // Invoice Table
    $html .= "<h2>Invoice History</h2>";
    $html .= "<table border='1' cellpadding='4'>";
    $html .= "<tr><th>Invoice No.</th><th>Invoice Date</th><th>Due Date</th><th>Outstanding Balance</th><th>Total Amount</th></tr>";

    foreach ($tenantData as $invoice) {
        $html .= "<tr>";
        $html .= "<td>{$invoice['invoiceNumber']}</td>";
        $html .= "<td>{$invoice['dateOfInvoice']}</td>";
        $html .= "<td>{$invoice['dateDue']}</td>";
        $html .= "<td>KES {$invoice['outstanding_balance']}</td>";
        $html .= "<td>KES {$invoice['total_amount']}</td>";
        $html .= "</tr>";
    }
    $html .= "</table>";

    // Water Usage Table
    $html .= "<h2>Water Usage</h2>";
    $html .= "<table border='1' cellpadding='4'>";
    $html .= "<tr><th>Previous Reading</th><th>Current Reading</th><th>Total Units</th><th>Rate (KES/m³)</th><th>Total Bill</th></tr>";
    $html .= "<tr>";
    $html .= "<td>{$tenantData[0]['previous_reading']} m³</td>";
    $html .= "<td>{$tenantData[0]['current_reading']} m³</td>";
    $html .= "<td>{$tenantData[0]['total_units']} m³</td>";
    $html .= "<td>KES {$tenantData[0]['water_rate']}</td>";
    $html .= "<td>KES {$tenantData[0]['total_consumption']}</td>";
    $html .= "</tr>";
    $html .= "</table>";

    // Payment History Table
    $html .= "<h2>Payment History</h2>";
    $html .= "<table border='1' cellpadding='4'>";
    $html .= "<tr><th>Date</th><th>Amount Paid</th><th>Mpesa Code</th><th>Comment</th></tr>";

    if (!empty($payments)) {
        foreach ($payments as $payment) {
            $html .= "<tr>";
            $html .= "<td>{$payment['dateofPayment']}</td>";
            $html .= "<td>KES {$payment['amountPaid']}</td>";
            $html .= "<td>{$payment['mpesaCode']}</td>";
            $html .= "<td>{$payment['comment']}</td>";
            $html .= "</tr>";
        }
    } else {
        $html .= "<tr><td colspan='4' align='center'>No payments found</td></tr>";
    }

    $html .= "</table>";
    $html .= "<p><strong>Total Payments:</strong> KES $totalPayments</p>";

    $pdf->writeHTML($html, true, false, true, false, '');

    // Output PDF
    ob_clean();
    $pdf->Output('tenant_report_' . $tenantId . '.pdf', 'D');

    exit();
} else {
    header("Location: new-report.php");
    exit();
}
?>
