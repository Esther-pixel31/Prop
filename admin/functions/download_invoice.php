<?php

ob_start(); // Start output buffering
require_once "db.php";
require_once "tcpdf/tcpdf.php";

if (isset($_GET['invoice_number']) && isset($_GET['format'])) {
    $invoiceNumber = $_GET['invoice_number'];
    $format = $_GET['format'];
    
    // Get invoice data with JOINs
    $sql = "
    SELECT 
        invoices.invoiceNumber, 
        invoices.dateOfInvoice, 
        invoices.dateDue, 
        tenants.tenant_name, 
        houses.house_name, 
        house_numbers.house_no, 
        house_numbers.rent_amount AS rent, 
        houses.garbage AS garbage, 
        water_readings.current_reading, 
        water_readings.previous_reading, 
        water_readings.total_units, 
        water_readings.water_rate, 
        water_readings.total_amount AS total_consumption, 
        invoices.amountDue AS outstanding_balance, 
        invoices.totalAmount AS total_amount 
    FROM 
        invoices 
    LEFT JOIN 
        tenants ON invoices.tenantID = tenants.tenantID 
    LEFT JOIN 
        house_numbers ON tenants.houseNumber = house_numbers.id 
    LEFT JOIN 
        houses ON house_numbers.house_id = houses.houseID 
    LEFT JOIN 
        water_readings ON tenants.tenantID = water_readings.tenant_id 
    WHERE 
        invoices.invoiceNumber = '$invoiceNumber' 
    GROUP BY 
        invoices.invoiceNumber";

    $result = mysqli_query($connection, $sql);
    $invoice = mysqli_fetch_assoc($result);
    
    if ($invoice) {
        if ($format === 'pdf') {
            ob_end_clean();
            ob_start();
            
            // Create PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Nyumbani Homes');
            $pdf->SetTitle('Invoice ' . $invoiceNumber);
            $pdf->SetSubject('Invoice');
            $pdf->AddPage();
            
            // Invoice Template
            $html = '<style>
                .header { text-align: center; font-size: 20px; font-weight: bold; }
                .company-details { text-align: right; font-size: 12px; }
                .divider { border-top: 2px solid black; margin: 10px 0; }
                .invoice-details { font-size: 14px; }
                .total { font-size: 16px; font-weight: bold; }
                .thank-you { font-size: 16px; font-weight: bold; text-align: center; margin-top: 20px; }
            </style>
            <table width="100%">
                <tr>
                    <td><img src="../images/icon.png" width="100" /></td>
                    <td class="company-details">
                        Nyumbani Homes<br>
                        Nairobi, Kenya.<br>
                        +254-746-932-222<br>
                        Estherwmmutua@gmail.com
                    </td>
            
                </tr>
            </table>
            <br>
            <div class="divider"></div>
            <table class="invoice-details" width="100%">
                <tr><td><b>Inv No.:</b></td><td>' . $invoice['invoiceNumber'] . '</td></tr>
                <tr><td><b>Inv Date.:</b></td><td>' . $invoice['dateOfInvoice'] . '</td></tr>
                <tr><td><b>Due Date:</b></td><td>' . $invoice['dateDue'] . '</td></tr>
                <tr><td><b>Tenant Name:</b></td><td>' . $invoice['tenant_name'] . '</td></tr>
                <tr><td><b>Property Name:</b></td><td>' . $invoice['house_name'] . '</td></tr>
                <tr><td><b>House No:</b></td><td>' . $invoice['house_no'] . '</td></tr>
            </table>
            <br>
            <div class="divider"></div>
            <table class="invoice-details" width="100%">
                <tr><td><b>Rent:</b></td><td>' . $invoice['rent'] . ' Kshs</td></tr>
                <tr><td><b>Garbage:</b></td><td>' . $invoice['garbage'] . ' Kshs</td></tr>
                <tr><td><b>Current Reading:</b></td><td>' . $invoice['current_reading'] . ' Units</td></tr>
                <tr><td><b>Previous Reading:</b></td><td>' . $invoice['previous_reading'] . ' Units</td></tr>
                <tr><td><b>Water Rate:</b></td><td>' . $invoice['water_rate'] . ' Kshs</td></tr>
                <tr><td><b>Total Consumption:</b></td><td>' . $invoice['total_consumption'] . ' Kshs</td></tr>
            </table>
            <br>
            <div class="divider"></div>
            <table class="invoice-details" width="100%">
                <tr><td><b>Outstanding Balance:</b></td><td>' . $invoice['outstanding_balance'] . ' Kshs</td></tr>
                <hr>
                <tr class="total"><td><b>Total Amount:</b></td><td>' . $invoice['total_amount'] . ' Kshs</td></tr>
            </table>
            <div class="divider"></div>
            <div class="thank-you">THANK YOU!!!</div>';
            
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output('invoice_'.$invoiceNumber.'.pdf', 'D');
            exit();
        }
    }
}

header("Location: ../invoices.php");
exit();
