<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pgnm="Nyumbani : Add Water Readings";
$error=' ';

//require the global file for errors
require_once "functions/errors.php";

ob_start();
require_once "functions/db.php";

// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['email']) || empty($_SESSION['email'])){
  header("location: login.php");
  exit;
}

if (is_logged_in_temporary()) {
    
    /*******************************************************
                    Introduce the admin header
    *******************************************************/
    require "admin_header0.php";

    /*******************************************************
                    Add the left panel
    *******************************************************/
    require "admin_left_panel.php";

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addreadings'])) {
        $tenant_id = $_POST['tname'];
        $previous_reading = $_POST['previous_reading'];
        $current_reading = $_POST['current_reading'];
        $total_units = $current_reading - $previous_reading;
        $water_rate = $_POST['water_rate'];
        $total_amount = $total_units * $water_rate;

        // Check for existing readings in the current month
        $query = "SELECT COUNT(*) FROM water_readings WHERE tenant_id = ? AND MONTH(reading_date) = MONTH(CURRENT_DATE()) AND YEAR(reading_date) = YEAR(CURRENT_DATE())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $tenant_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $error = "A water reading for this tenant has already been recorded this month.";
        } else {
            // Insert into the database
            $query = "INSERT INTO water_readings (tenant_id, previous_reading, current_reading, total_units, water_rate, total_amount) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iidddd", $tenant_id, $previous_reading, $current_reading, $total_units, $water_rate, $total_amount);

            if ($stmt->execute()) {
                $success_message = "Water reading added successfully.";
            } else {
                $error = "Error adding water reading: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Fetch the last month's current reading for the selected tenant
    if (isset($_POST['tname'])) {
        $tenant_id = $_POST['tname'];
        $query = "SELECT current_reading FROM water_readings WHERE tenant_id = ? AND MONTH(reading_date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(reading_date) = YEAR(CURRENT_DATE()) ORDER BY reading_date DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $tenant_id);
        $stmt->execute();
        $stmt->bind_result($last_current_reading);
        $stmt->fetch();
        $stmt->close();
    }
?>

<div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title"><?php echo 'Hey '.$username.'!';?></h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12"> 
                        <ol class="breadcrumb">
                            <li><a href="index.php">Dashboard</a></li>
                            <li><a href="water.php">Tenants</a></li>
                            <li class="active">New</li>
                        </ol>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!--.row-->
                <div class="row">
                    <div class="col-md-12">
                        
                        <div class="white-box">
                            <h3 class="box-title m-b-0"><i class="fa fa-user fa-3x"></i> Admit New Water Readings</h3>
                            <p class="text-muted m-b-30 font-13"> Fill in the form below: </p>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <form action="new-water.php" method="post" enctype="multipart/form-data"> 
                                        

                                    <div class="form-group">
                                        <label for="tname">Tenant Name:</label>
                                        <select name="tname" id="tname" class="form-control" required>
                                            <option value="">Select Tenant</option>
                                            <?php
                                            // Fetch tenants from the database
                                            $query = "SELECT t.tenantID, t.tenant_name 
                                                      FROM tenants t 
                                                      WHERE NOT EXISTS (
                                                          SELECT 1 FROM water_readings w 
                                                          WHERE w.tenant_id = t.tenantID 
                                                          AND MONTH(w.reading_date) = MONTH(CURRENT_DATE()) 
                                                          AND YEAR(w.reading_date) = YEAR(CURRENT_DATE())
                                                      )";
                                            $result = $conn->query($query);

                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . $row['tenantID'] . '">' . $row['tenant_name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                        <div class="form-group">
                                        <label for="house_name">House Name:</label>
                                        <input type="text" id="house_name" class="form-control" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="house_number">House Number:</label>
                                            <input type="text" id="house_number" class="form-control" readonly>
                                        </div>


                                        <div class="form-group">
                                            <label for="previous_reading">Previous Water Reading:</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-tint"></i></div>
                                                    <input type="number" class="form-control" id="previous_reading" name="previous_reading" min="0" value="<?php echo isset($last_current_reading) ? $last_current_reading : ''; ?>" required>
                                                </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="current_reading">Current Water Reading:</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-tint"></i></div>
                                                    <input type="number" class="form-control" id="current_reading" name="current_reading" min="0" required>
                                                </div>
                                        </div>

                                        <button type="button" id="calculate" class="btn btn-success btn-lg waves-effect waves-light m-r-10 center"><i class="fa fa-plus-circle fa-lg"></i> Calculate</button>
                                        <br>
                                        <br>
                                        <div class="form-group">
                                            <label for="Total_units">Total Units Consumed:</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-tint"></i></div>
                                                    <input type="number" class="form-control" id="Total_units" name="Total_amount" min="0" required readonly>
                                                </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="water_rate">Water Rate:</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-tint"></i></div>
                                                        <select class="form-control" id="water_rate" name="water_rate" required>
                                                            <option value="130">130 Kshs per unit</option>
                                                            <option value="150">150 Kshs per unit</option>
                                                            <option value="170">170 Kshs per unit</option>
                                                            <option value="200">200 Kshs per unit</option>
                                                        </select>
                                                </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="Total_amount">Total Amount:</label>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-tint"></i></div>
                                                    <input type="number" class="form-control" id="Total_amount" name="Total_amount" min="0" required readonly>
                                                </div>
                                        </div>

                                        <button type="submit" name="addreadings" class="btn btn-success btn-lg waves-effect waves-light m-r-10 center"><i class="fa fa-plus-circle fa-lg"></i> Add Water Reading</button>
                                    </form>
                                    </div>
                            </div>
                        </div>
                </div>
            </div>
    </div>
</div>
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
                                <li><a href="javascript:void(0)" theme="gray-dark-theme">9</a></li>
                                <li><a href="javascript:void(0)" theme="blue-dark" class="blue-theme">10</a></li>
                                <li><a href="javascript:void(0)" theme="purple-dark" class="purple-theme">11</a></li>
                                <li><a href="javascript:void(0)" theme="megna-dark" class="megna-theme">12</a></li>
                            </ul>
                            </div>
                    </div>
                </div>
                <!-- /.right-sidebar -->
            </div>
            <!-- /.container-fluid -->
            <footer class="footer text-center"> 2024 &copy; Company Admin </footer>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#calculate').click(function() {
            var previousReading = parseFloat($('#previous_reading').val());
            var currentReading = parseFloat($('#current_reading').val());
            var totalUnits = currentReading - previousReading;
            $('#Total_units').val(totalUnits);

            // Calculate total amount based on selected water rate
            var waterRate = parseFloat($('#water_rate').val());
            var totalAmount = totalUnits * waterRate;
            $('#Total_amount').val(totalAmount);
        });

        $('#house').change(function() {
            $.ajax({
                type: 'POST',
                url: 'fetch_tenants.php', // New PHP file to handle tenant fetching
                data: { month: new Date().getMonth() + 1, year: new Date().getFullYear() },
                dataType: 'json',
                success: function(response) {
                    $('#tname').empty(); // Clear existing options
                    $.each(response, function(index, tenant) {
                        $('#tname').append('<option value="' + tenant.tenantID + '">' + tenant.tenant_name + '</option>');
                    });
                }
            });
        });

        $('#tname').change(function() {
            var tenantID = $(this).val(); // Get selected tenant ID

            if (tenantID) {
                // AJAX request to fetch tenant's house details
                $.ajax({
                    type: 'POST',
                    url: 'fetch_tenant_house_details.php',
                    data: { tenant_id: tenantID },
                    dataType: 'json',
                    success: function(response) {
                        if (!response.error) {
                            // Populate the house name and house number fields
                            $('#house_name').val(response.house_name);
                            $('#house_number').val(response.house_no);
                        } else {
                            console.error(response.error);
                            alert('Failed to fetch house details for the selected tenant.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });

                // Fetch the last month's current reading for the selected tenant
                $.ajax({
                    type: 'POST',
                    url: 'fetch_last_reading.php',
                    data: { tenant_id: tenantID },
                    dataType: 'json',
                    success: function(response) {
                        if (!response.error) {
                            $('#previous_reading').val(response.last_current_reading);
                        } else {
                            console.error(response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                // Clear fields if no tenant is selected
                $('#house_name').val('');
                $('#house_number').val('');
                $('#previous_reading').val('');
            }
        });
        $('#house').change(function() {
    $.ajax({
        type: 'POST',
        url: 'fetch_tenants.php', // New PHP file to handle tenant fetching
        data: { month: new Date().getMonth() + 1, year: new Date().getFullYear() },
        dataType: 'json',
        success: function(response) {
            $('#tname').empty(); // Clear existing options
            $.each(response, function(index, tenant) {
                $('#tname').append('<option value="' + tenant.tenantID + '">' + tenant.tenant_name + '</option>');
            });
        }
    });
});
    });

    </script>
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
            
        </script>
    <!--END of local JS -->

</body>

</html>
<?php
}
 else
 {
    header('location:../index.php');
 }
 ?>
