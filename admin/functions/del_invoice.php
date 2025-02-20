<?php
require_once "db.php"; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['invoice_number'])) {
        $invoice_number = $_POST['invoice_number'];

        // Prepare the SQL DELETE statement
        $sql = "DELETE FROM invoices WHERE invoiceNumber = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $invoice_number);

        if ($stmt->execute()) {
            // Redirect with success message
            header("Location: ../invoices.php?deleted=true");
        } else {
            // Redirect with error message
            header("Location: ../invoices.php?del_error=true");
        }

        $stmt->close();
    } else {
        // Redirect with error message if invoice number is not set
        header("Location: ../invoices.php?del_error=true");
    }
} else {
    // Redirect if the request method is not POST
    header("Location: ../invoices.php?del_error=true");
}
?>
