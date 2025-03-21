<?php 

// Page name
$pgnm = 'Nyumbani: Admit a Tenant';
$error = ' ';

// Start session
session_start();

// Include database connection & error handling
require_once "functions/db.php";
require_once "functions/errors.php";




// Redirect if user is not logged in
if (!is_logged_in_temporary()) {
    header('location:../index.php');
    exit();
}

// Fetch all tenants
$tenantsQuery = "SELECT tenantID, tenant_name FROM tenants ORDER BY tenant_name";
$tenantsResult = $connection->query($tenantsQuery);
$tenants = $tenantsResult->fetch_all(MYSQLI_ASSOC);


// Handle report generation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $month = $_POST['month'];
    $year = $_POST['year'];
    $reportDate = "$year-$month-01";

    // Get total payments
    $paymentsQuery = "SELECT SUM(amountPaid) AS total_payments 
                      FROM payments 
                      WHERE DATE_FORMAT(dateofPayment, '%Y-%m') = ?";
    $stmt = $connection->prepare($paymentsQuery);
    $stmt->bind_param("s", $reportDate);
    $stmt->execute();
    $paymentsResult = $stmt->get_result();
    $totalPayments = $paymentsResult->fetch_assoc()['total_payments'] ?? 0;


    // Get total active tenants
    $tenantsQuery = "SELECT COUNT(*) AS total_tenants 
                     FROM tenants 
                     WHERE DATE_FORMAT(dateAdmitted, '%Y-%m') <= ?";
    $stmt = $connection->prepare($tenantsQuery);
    $stmt->bind_param("s", $reportDate);
    $stmt->execute();
    $tenantsResult = $stmt->get_result();
    $totalTenants = $tenantsResult->fetch_assoc()['total_tenants'] ?? 0;


    // Get total invoices
    $invoicesQuery = "SELECT COUNT(*) AS total_invoices 
                      FROM invoices 
                      WHERE DATE_FORMAT(dateOfInvoice, '%Y-%m') = ?";
    $stmt = $connection->prepare($invoicesQuery);
    $stmt->bind_param("s", $reportDate);
    $stmt->execute();
    $invoicesResult = $stmt->get_result();
    $totalInvoices = $invoicesResult->fetch_assoc()['total_invoices'] ?? 0;


    // Insert into monthly_reports
    $insertQuery = "INSERT INTO monthly_reports 
                   (report_month, total_payments, total_tenants, total_invoices) 
                   VALUES (?, ?, ?, ?)";
    $stmt = $connection->prepare($insertQuery);
    $stmt->bind_param("siii", $reportDate, $totalPayments, $totalTenants, $totalInvoices);
    $stmt->execute();

    
    header("Location:reports.php");
    exit();
}

// Load UI components
require "admin_header0.php";
require "admin_left_panel.php";

?>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title"><?php echo 'Hey ' . ($_SESSION['username'] ?? 'Admin') . '!'; ?></h4>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
                <ol class="breadcrumb">
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="reports.php">Reports</a></li>
                    <li class="active">New</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div style="">
                    <?php echo $error; ?>
                </div>
                <div class="white-box">
                    <h3 class="box-title m-b-0"><i class="fa fa-file-text fa-3x"></i> Generate Reports</h3>
                    <p class="text-muted m-b-30 font-13"> Select report type: </p>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Individual Tenant Report</h4>
                            <form action="generate-tenant-report.php" method="post">
                                <div class="form-group">
                                    <label for="tenant">Select Tenant:</label>
                                    <select name="tenant_id" id="tenant" class="form-control" required>
                                        <?php foreach ($tenants as $tenant): ?>
                                            <option value="<?= $tenant['tenantID'] ?>">
                                                <?= htmlspecialchars($tenant['tenant_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Generate Tenant Report</button>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<footer class="footer text-center"> 2025 &copy; Company Admin </footer>

<!-- Scripts -->
<script src="../plugins/bower_components/jquery/dist/jquery.min.js"></script>
<script src="bootstrap/dist/js/tether.min.js"></script>
<script src="bootstrap/dist/js/bootstrap.min.js"></script>
<script src="../plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>
<script src="js/jquery.slimscroll.js"></script>
<script src="js/waves.js"></script>
<script src="js/custom.min.js"></script>
<script src="js/jasny-bootstrap.js"></script>
<script src="../plugins/bower_components/styleswitcher/jQuery.style.switcher.js"></script>

</body>
</html>
