<?php 

    //a page name
    $pgnm='Nyumbani: New Payment';
    $error=' ';
    $timesnap=date('Y-m-d : H:i:s');

    //start sessions 
    ob_start();

    //require a connector
    require_once "functions/db.php";

    //require the global file for errors
    require_once "functions/errors.php";


    // Initialize the session
    session_start();

    // If current user is not logged in, redirect to index otherwise, allow access
     if (is_logged_in_temporary()) {
        //allow access

        //take requests & actions

             /*****************************************************
                               action add a new payment
             ***************************************************/
            if (isset($_POST['newPayment'])) {
                // Step 1: Collect data
                $tenantId = uncrack($_POST['tenID']);
                $invoiceNumber = uncrack($_POST['invoiceNumber']); // starting invoice
                $amountExpected = uncrack($_POST['amountDue']);
                $amountPaid = uncrack($_POST['paidAmount']);
                $mpesaCode = uncrack($_POST['mpesa']);
            
                $paymentDate = date('Y-m-d');
                $timesnap = date('Y-m-d : H:i:s');
            
                // Get tenant info
                $tenantData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `tenant_name`, `phone_number` FROM tenants WHERE tenantID='$tenantId'"));
                $tnm = $tenantData['tenant_name'];
                $phone = $tenantData['phone_number'];
            
                // Step 2: Begin atomic transaction
                $mysqli->autocommit(FALSE);
                $state = true;
            
                // Step 3: Log base payment with temp balance (we'll update after allocations)
                $balance = $amountExpected - $amountPaid;

                $sql_payment = "INSERT INTO payments (tenantID, invoiceNumber, expectedAmount, amountPaid, balance, mpesaCode, dateofPayment)
                VALUES ('$tenantId', '$invoiceNumber', '$amountExpected', '$amountPaid', '$balance', '$mpesaCode', '$paymentDate')";
                
                $state = $state && $mysqli->query($sql_payment);
                $payment_id = $mysqli->insert_id;
            
                // Step 4: Auto-allocate payment to unpaid invoices
                $remaining = $amountPaid;
                $sql_unpaid = "SELECT * FROM invoices WHERE tenantID='$tenantId' AND status='unpaid' ORDER BY dateOfInvoice ASC";
                $result = $mysqli->query($sql_unpaid);
            
                while ($inv = $result->fetch_assoc()) {
                    if ($remaining <= 0) break;
            
                    $inv_no = $inv['invoiceNumber'];
                    $due = $inv['amountDue'];
            
                    $allocate = min($remaining, $due);
                    $remaining -= $allocate;
                    $new_balance = $due - $allocate;
                    $status = ($new_balance <= 0) ? 'paid' : 'unpaid';
            
                    // Update invoice
                    $state = $state && $mysqli->query("UPDATE invoices SET amountDue='$new_balance', status='$status' WHERE invoiceNumber='$inv_no'");
            
                    // Record allocation
                    $state = $state && $mysqli->query("INSERT INTO payment_invoice_allocations (payment_id, invoiceNumber, amount_allocated)
                                                       VALUES ('$payment_id', '$inv_no', '$allocate')");
                }
            
                // Step 5: Update the payment record with any leftover balance
                $state = $state && $mysqli->query("UPDATE payments SET balance='$remaining' WHERE paymentID='$payment_id'");
            
                // Step 6: Log transaction
                $sql_transactions = "INSERT INTO transactions (`actor`, `time`, `description`)
                                     VALUES ('Admin ($username)', '$timesnap', '$username added payment of KSh $amountPaid for $tnm')";
                $state = $state && $mysqli->query($sql_transactions);
            
                // Step 7: Commit or rollback
                if ($state) {
                    $mysqli->commit();
                    header("location:payments.php?state=8");
                } else {
                    $mysqli->rollback();
                    header("location:new-payment.php?state=9");
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
                        <h4 class="page-title"><?php echo 'Howdy, '.$username.'!';?></h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
                        <ol class="breadcrumb">
                            <li><a href="index.php">Dashboard</a></li>
                            <li><a href="payments.php">Payments</a></li>
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
                            <h3 class="box-title m-b-0"><i class="fa fa-money fa-3x"></i> Add A New Rent Payment</h3>
                            <p class="text-muted m-b-30 font-13"> Fill in the form below: </p>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <form action="new-payment.php" method="post">
                                        
                                        <div class="form-group">
                                            <label for="tname">Choose a Tenant: <span style="color:red">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-user"></i></div>

                                                <select  required="" id="tname" name="invoiceNumber" class="form-control" onchange="requestInvoice(this.value);">
                                                    <option value="">**Select a tenant**</option>
                                                    <?php
                                                        $sq1="SELECT `invoiceNumber`,`tenant_name`,`tenantID` from `invoicesView` where `status`='unpaid' order by `tenantID` desc";
                                                        $rec=mysqli_query($conn,$sq1);
                                                        while ($row=mysqli_fetch_array($rec,MYSQLI_BOTH)) {
                                                            $tenant=$row['tenant_name'];
                                                            $tenid=$row['invoiceNumber'];
                                                            echo "<option value='$tenid'> $tenant (<i> $tenid </i>) </option> ";
                                                        }
                                                        ?>
                                                </select> 
                                            </div>
                                        </div>

                                        <div id="txtInvoice">
                                            
                                        </div>

                                        <div class="form-group">
                                            <label for="paidAmount">Amount Paid: <span style="color:red">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-usd"></i></div>
                                                <input type="number" required="" min="0" name="paidAmount" class="form-control" id="paidAmount" placeholder="Enter Amount"> </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="mpesa">Mpesa Code/ Bank Code: </label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-usd"></i></div>
                                                <input type="text" min="0" name="mpesa" class="form-control" id="mpesa" placeholder="Mpesa code e.g. XX00XXYY" style=" background-color: #000; color: #fff; font-weight: 700;text-transform: uppercase;"> </div>
                                        </div>

                            

                                        <button type="submit" name="newPayment" class="btn btn-success btn-lg waves-effect waves-light m-r-10 center"><i class="fa fa-plus-circle fa-lg"></i> Update this Payment</button>
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
                                <li><a href="javascript:void(0)" theme="purple-dark" class="purple-dark-theme">11</a></li>
                                <li><a href="javascript:void(0)" theme="megna-dark" class="megna-dark-theme">12</a></li>
                            </ul>
                            </div>
                    </div>
                </div>
                <!-- /.right-sidebar -->
            </div>
            <!-- /.container-fluid -->
            <footer class="footer text-center"> 2025 &copy; Company Admin </footer>
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
            //ajax method to source invoice details selected

            function requestInvoice(str) 
            {
              if (str == "") {
                document.getElementById("txtInvoice").innerHTML = "";
                return;
              } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                  if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtInvoice").innerHTML = this.responseText;
                  }
                };
                xmlhttp.open("GET","functions/request_invoice.php?q="+str,true);
                xmlhttp.send();
              }
            }
            
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