<?php
require_once "db.php";

if (isset($_GET["q"])) {
  $invoiceNumber = $_GET["q"];

  // Fetch invoice & tenant data
  $sql_invoice = "SELECT * FROM invoicesView WHERE invoiceNumber = '$invoiceNumber' LIMIT 1";
  $res_invoice = mysqli_query($conn, $sql_invoice);

  if (mysqli_num_rows($res_invoice) > 0) {
      $invoice = mysqli_fetch_assoc($res_invoice);
      $tenantID = $invoice['tenantID'];
      $tenantName = $invoice['tenant_name'];

      // Fetch all unpaid invoices for this tenant
      $sql_unpaid = "SELECT invoiceNumber, amountDue, dateOfInvoice FROM invoices WHERE tenantID='$tenantID' AND status='unpaid'";
      $result_unpaid = mysqli_query($conn, $sql_unpaid);

      $totalDue = 0;
      $invoiceList = '';

      while ($row = mysqli_fetch_assoc($result_unpaid)) {
          $invNo = $row['invoiceNumber'];
          $due = $row['amountDue'];
          $date = $row['dateOfInvoice'];
          $totalDue += $due;

          $invoiceList .= "<li>Invoice #: <strong>$invNo</strong> - Due: KSh <strong>$due</strong> ($date)</li>";
      }

      echo "<div class='alert alert-info'>
              <strong>Tenant Name:</strong> $tenantName<br>
              <strong>Total Due (All Unpaid Invoices):</strong> KSh $totalDue
            </div>
            <input type='hidden' name='tenID' value='$tenantID'>
            <input type='hidden' name='amountDue' value='$totalDue'>
            <ul style='list-style: none; padding-left: 0;'>$invoiceList</ul>";
  } else {
      echo "<div class='alert alert-warning'>Invoice not found.</div>";
  }
} else {
  echo "<div class='alert alert-danger'>Invalid request.</div>";
}
?>