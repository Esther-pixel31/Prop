<?php

require_once "functions/db.php";
session_start();

// Ensure user is logged in
if (!is_logged_in_temporary()) {
    header('location:../index.php');
    exit();
}

// Fetch reports from the database
$sql = "SELECT 
            r.report_month, 
            r.total_payments, 
            r.total_tenants, 
            r.total_invoices,
            t.tenant_name,
            h.house_name,
            t.rent_amount,
            t.dateAdmitted
        FROM monthly_reports r
        LEFT JOIN tenants t ON t.tenantID = r.tenant_id
        LEFT JOIN house_numbers hn ON t.houseNumber = hn.id
        LEFT JOIN houses h ON hn.house_id = h.houseID
        ORDER BY r.report_month DESC";

$result = query($sql);
$reports = $result->fetch_all(MYSQLI_ASSOC);

require "admin_header0.php";
require "admin_left_panel.php";
?>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-12">
                <h4 class="page-title">Monthly Reports</h4>
            </div>
        </div>

        <div class="white-box">
            <h3 class="box-title">Report Details</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Report Month</th>
                            <th>Total Payments</th>
                            <th>Total Tenants</th>
                            <th>Total Invoices</th>
                            <th>Tenant Name</th>
                            <th>House Name</th>
                            <th>Rent Amount</th>
                            <th>Admission Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report) : ?>
                            <tr>
                                <td><?= $report['report_month'] ?></td>
                                <td><?= number_format($report['total_payments'], 2) ?></td>
                                <td><?= $report['total_tenants'] ?></td>
                                <td><?= $report['total_invoices'] ?></td>
                                <td><?= $report['tenant_name'] ?></td>
                                <td><?= $report['house_name'] ?></td>
                                <td><?= number_format($report['rent_amount'], 2) ?></td>
                                <td><?= $report['dateAdmitted'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require "admin_footer.php"; ?>
