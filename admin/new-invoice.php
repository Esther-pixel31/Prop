<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

    //a page name
    $pgnm='Nyumbani: New Invoice';
    $error=' ';
    $timesnap=date('Y-m-d : H:i:s');

    //start sessions 
    ob_start();

    //require a connector
    require_once "functions/db.php";

    //require the global file for errors
    require_once "functions/errors.php";

    // Function to check if a tenant has been invoiced for the current month
function tenantInvoicedForCurrentMonth($tenantID, $conn) {
    $currentMonth = date('Y-m');
    $query = "SELECT COUNT(*) FROM invoices WHERE tenantID = ? AND DATE_FORMAT(dateOfInvoice, '%Y-%m') = ? AND amountDue > 0";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("is", $tenantID, $currentMonth);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            return $count > 0;
        } else {
            return false; // Error preparing query
        }
    }

    // Fetch tenant names for the dropdown
    $tenant_query = "SELECT tenantID, tenant_name FROM tenants";
    $tenant_result = $conn->query($tenant_query);
    $tenants = [];
    if ($tenant_result->num_rows > 0) {
        while ($row = $tenant_result->fetch_assoc()) {
            if (!tenantInvoicedForCurrentMonth($row['tenantID'], $conn)) {
                $tenants[] = $row;
            }
        }
    }


    // Initialize the session
    session_start();

    

    // If current user is not logged in, redirect to index otherwise, allow access
     if (is_logged_in_temporary()) {
        
        //allow access

        function generateInvoiceNumber($conn) {
            $prefix = "INV";
            $date = date("Ymd");
            $query = "SELECT MAX(invoiceNumber) AS max_invoice FROM invoices";
            $result = $conn->query($query);
            $maxInvoice = $result->fetch_assoc()['max_invoice'];
            $nextInvoiceNumber = $maxInvoice ? intval(substr($maxInvoice, -4)) + 1 : 1;
            return $prefix . $date . str_pad($nextInvoiceNumber, 4, '0', STR_PAD_LEFT);
        }

        // Function to get the outstanding balance for a tenant
        function getOutstandingBalance($tenantID, $conn) {
            $balance_query = "SELECT outstanding_balance FROM tenantsdetailsview WHERE tenantID = ?";
            if ($stmt = $conn->prepare($balance_query)) {
                $stmt->bind_param("i", $tenantID);
                $stmt->execute();
                $stmt->bind_result($balance);
                if ($stmt->fetch()) {
                    return $balance !== null ? $balance : 0; // Set to 0 if balance is NULL
                } else {
                    return 0; // No balance found, set to 0
                }
                $stmt->close();
            } else {
                return 0; // Error preparing query, set balance to 0
            }
        }

        

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['tname'])) {
                $tenantID = intval($_POST['tname']);
                $invoiceNumber = generateInvoiceNumber($conn);
                $invoiceDate = $_POST['ddate'];
                $dueDate = $_POST['dddate'];
                $rent = floatval($_POST['rent']);
                $garbage = floatval($_POST['garbage']);
                $currentReading = floatval($_POST['current_reading']);
                $previousReading = floatval($_POST['previous_reading']);
                $waterRate = floatval($_POST['water_rate']);
                $outstandingBalance = getOutstandingBalance($tenantID, $conn);

// Calculate total consumption
$totalConsumption = ($currentReading - $previousReading) * $waterRate;

// Calculate total amount
$totalAmount = $rent + $garbage + $totalConsumption;

// Insert invoice data into the database
$insert_query = "INSERT INTO invoices (invoiceNumber, tenantID, dateOfInvoice, dateDue, amountDue, totalAmount) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE amountDue = amountDue + VALUES(amountDue)";
if ($stmt = $conn->prepare($insert_query)) {
    $stmt->bind_param("sissdd", $invoiceNumber, $tenantID, $invoiceDate, $dueDate, $totalAmount, $totalAmount);
   
   
    if ($stmt->execute()) {
        echo "Invoice created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Error preparing query: " . $conn->error;
}
        }
    }
        

        /*******************************************************
                    introduce the admin header
        *******************************************************/
        require "admin_header0.php";


        /*******************************************************
                    Add the left panel
        *******************************************************/
        require "admin_left_panel.php";

       
    
    ?>

        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title"><?php echo 'Hey '.$username.'!';?></h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
                        <ol class="breadcrumb">
                            <li><a href="index.php">Dashboard</a></li>
                            <li><a href="invoices.php">Invoices</a></li>
                            <li class="active">New</li>
                        </ol>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!--.row-->
                <div class="row">
                    <div class="col-md-12">
                        <div style="">
                            <?php 
                            echo $error;
                            ?>
                        </div>
                        <div class="white-box">
                            <h3 class="box-title m-b-0"><i class="fa fa-credit-card fa-3x"></i> Add A New Rental Invoice</h3>
                            <p class="text-muted m-b-30 font-13"> Fill in the form below: </p>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <form action="new-invoice.php" method="post">
                                    <div class="form-group">
                                            <label for="tname">Tenant Name: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <select name="tname" id="tname" style="width:100%;" required>
                                                    <option value="">Select Tenant</option>
                                                    <?php foreach ($tenants as $tenant): ?>
                                                        <option value="<?php echo $tenant['tenantID']; ?>"><?php echo $tenant['tenant_name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>


                                    <div class="form-group">
                                            <label for="ddate">Invoice Date: </label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="date" name="ddate" class="form-control" id="ddate" placeholder="Choose Date"> </div>
                                    </div>

                                    <div class="form-group">
                                            <label for="dddate">Invoice Due Date: </label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="date" name="dddate" class="form-control" id="dddate" placeholder="Choose Date"> </div>
                                    </div>


                                        <div class="form-group">
                                            <label for="hname">House Name: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <textarea name="hname" id="hname" cols="6" placeholder="e.g. this is the house name" style="width:100%;" readonly></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="hnumber">House No.: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <textarea name="hnumber" id="hnumber" cols="6" placeholder="e.g. this is the house number" style="width:100%;" readonly></textarea>
                                            </div>
                                        </div>
       
                                        <div class="form-group">
                                            <label for="rent">Rent: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <input type="text" name="rent" id="rent" class="form-control" placeholder="Rent Amount" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="garbage">Garbage: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <input type="text" name="garbage" id="garbage" class="form-control" placeholder="Garbage Fee" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="current_reading">Current Reading: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <input type="text" name="current_reading" id="current_reading" class="form-control" placeholder="Current Reading" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="previous_reading">Previous Reading: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <input type="text" name="previous_reading" id="previous_reading" class="form-control" placeholder="Previous Reading" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="total_units">Total Units: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <input type="text" name="total_units" id="total_units" class="form-control" placeholder="Total Units" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="water_rate">Water Rate: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <input type="text" name="water_rate" id="water_rate" class="form-control" placeholder="Water Rate" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="total_con">Total Consumption: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <input type="text" name="total_con" id="total_con" class="form-control" placeholder="Total Consumption" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="balance">Outstanding Balance: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <input type="text" name="balance" id="balance" class="form-control" placeholder="Outstanding Balance" readonly>
                                            </div>
                                        </div>

                                        <button type="button" id="calculate" class="btn btn-success btn-lg waves-effect waves-light m-r-10 center"><i class="fa fa-plus-circle fa-lg"></i> Calculate</button>
<br>
<br>

                                        <div class="form-group">
                                            <label for="t_amount">Total Amount: *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                                <input type="text" name="t_amount" id="t_amount" class="form-control" placeholder="Total Amount" readonly>
                                            </div>
                                        </div>

                                        <button type="submit" name="addInvoice" class="btn btn-success btn-lg waves-effect waves-light m-r-10 center"><i class="fa fa-plus-circle fa-lg"></i> Add Invoice</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
           

                </div>
                <!--./row-->
               
           
               
                <!-- .right-sidebar -->
                <div class="right-sidebar">
                    <div class="slimscrollright">
                        <div class="rpanel-title"> Service Panel <span><i class="ti-close right-side-toggle"></i></span> </div>
                        <div class="r-panel-body">
                            <ul>
                                <li><b>Layout Options</b></li>
                                <li>
                                    <div class="checkbox checkbox-info">
                                        <input id="checkbox1" type="checkbox" class="fxhdr">
                                        <label for="checkbox1"> Fix Header </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="checkbox checkbox-warning">
                                        <input id="checkbox2" type="checkbox" checked="" class="fxsdr">
                                        <label for="checkbox2"> Fix Sidebar </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="checkbox checkbox-success">
                                        <input id="checkbox4" type="checkbox" class="open-close">
                                        <label for="checkbox4"> Toggle Sidebar </label>
                                    </div>
                                </li>
                            </ul>
                            <ul id="themecolors" class="m-t-20">
                                <li><b>With Light sidebar</b></li>
                                <li><a href="javascript:void(0)" theme="default" class="default-theme">1</a></li>
                                <li><a href="javascript:void(0)" theme="green" class="green-theme">2</a></li>
                                <li><a href="javascript:void(0)" theme="gray" class="yellow-theme">3</a></li>
                                <li><a href="javascript:void(0)" theme="blue" class="blue-theme working">4</a></li>
                                <li><a href="javascript:void(0)" theme="purple" class="purple-theme">5</a></li>
                                <li><a href="javascript:void(0)" theme="megna" class="megna-theme">6</a></li>
                                <li><b>With Dark sidebar</b></li>
                                <br/>
                                <li><a href="javascript:void(0)" theme="default-dark" class="default-dark-theme">7</a></li>
                                <li><a href="javascript:void(0)" theme="green-dark" class="green-dark-theme">8</a></li>
                                <li><a href="javascript:void(0)" theme="gray-dark" class="yellow-dark-theme">9</a></li>
                                <li><a href="javascript:void(0)" theme="blue-dark" class="blue-dark-theme">10</a></li>
                                <li><a href="javascript:void(0)" theme="purple-dark" class="purple-theme">11</a></li>
                                <li><a href="javascript:void(0)" theme="megna-dark" class="megna-theme">12</a></li>
                            </ul>
                            </div>
                    </div>
                </div>
                <!-- /.right-sidebar -->
            </div>

            <footer class="footer text-center"> 2018 &copy; Company Admin </footer>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
    <!-- jQuery -->
    <script src="../plugins/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="bootstrap/dist/js/tether.min.js"></script>
    <script src="bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../plugins/bower_components/bootstrap-extension/js/bootstrap-extension.min.js"></script>
    <!-- Menu Plugin JavaScript -->
    <script src="../plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>
    <!--slimscroll JavaScript -->
    <script src="js/jquery.slimscroll.js"></script>
    <!--Wave Effects -->
    <script src="js/waves.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="js/custom.min.js"></script>
    <script src="js/jasny-bootstrap.js"></script>
    <!--Style Switcher -->
    <script src="../plugins/bower_components/styleswitcher/jQuery.style.switcher.js"></script>

    <!-- Local Javascript -->
        <script type="text/javascript">
            $(document).ready(function() {
                $('#tname').change(function() {
                    var tenantId = $(this).val();
                    if (tenantId) {
                        $.ajax({
                            type: 'POST',
                            url: 'house_details.php',
                            data: { tenant_id: tenantId },
                            dataType: 'json',
                            success: function(response) {
                                if (response.error) {
                                    alert(response.error);
                                } else {
                                    $('#hname').val(response.house_name);
                                    $('#hnumber').val(response.house_no);
                                    $('#rent').val(response.rent);
                                    $('#garbage').val(response.garbage);
                                    $('#current_reading').val(response.current_reading);
                                    $('#previous_reading').val(response.previous_reading);
                                    $('#water_rate').val(response.water_rate);
                                    $('#total_units').val(response.total_units);
                                    $('#total_con').val(response.total_consumption);
                                    $('#balance').val(response.outstanding_balance);
                                }
                            },
                            error: function() {
                                alert('Error fetching data.');
                            }
                        });
                    } else {
                        // Clear fields if no tenant is selected
                        $('#hname').val('');
                        $('#hnumber').val('');
                        $('#rent').val('');
                        $('#garbage').val('');
                        $('#current_reading').val('');
                        $('#previous_reading').val('');
                        $('#water_rate').val('');
                        $('#total_units').val('');
                        $('#total_con').val('');
                        $('#balance').val('');
                    }
                });
            });
        </script>

<script>
document.getElementById('calculate').addEventListener('click', function() {
    // Get values from the form fields
    var rent = parseFloat(document.getElementById('rent').value) || 0;
    var garbage = parseFloat(document.getElementById('garbage').value) || 0;
    var currentReading = parseFloat(document.getElementById('current_reading').value) || 0;
    var previousReading = parseFloat(document.getElementById('previous_reading').value) || 0;

    var waterRate = parseFloat(document.getElementById('water_rate').value) || 0;

    // Calculate total consumption
    var totalConsumption = (currentReading - previousReading) * waterRate;

    console.log("Rent:", rent);
    console.log("Garbage:", garbage);
    console.log("Total Consumption:", totalConsumption);
    
    // Calculate total amount
    var totalAmount = rent + garbage + totalConsumption; // Corrected variable name

    // Update the total amount field
    document.getElementById('t_amount').value = totalAmount.toFixed(2);
});
</script>
    <!--END of local JS -->

</body>

</html>
<?php
}
else{
    header('location:../index.php');
}
?>
