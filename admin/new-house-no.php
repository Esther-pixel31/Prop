<?php
ob_start(); // Start output buffering
$pgnm = "Nyumbani : New Houses numbers";
$error = ' ';
session_start();
require_once "functions/db.php";
require_once "functions/errors.php";

if (!is_logged_in_temporary()) {
    header('Location: ../index.php');
    exit();
}

require "admin_header0.php";
require "admin_left_panel.php";

// Query to retrieve the list of houses and their IDs
$sql = "SELECT houseID, house_name FROM houses";
$result = mysqli_query($connection, $sql);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Enable error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Sanitize inputs
        $house_id = mysqli_real_escape_string($connection, $_POST['house_id']);
        $house_no = mysqli_real_escape_string($connection, $_POST['house_no']);

        // Check the number of rooms for the selected house
        $room_count_query = "SELECT number_of_rooms FROM houses WHERE houseID = '$house_id'";
        $room_count_result = mysqli_query($connection, $room_count_query);
        $room_count_data = mysqli_fetch_assoc($room_count_result);
        $numofrooms = $room_count_data['number_of_rooms'];

    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Sanitize inputs
    $house_id = mysqli_real_escape_string($connection, $_POST['house_id']);
    $house_no = mysqli_real_escape_string($connection, $_POST['house_no']);

    // Check the number of existing house numbers for the selected house
    $existing_house_numbers_query = "SELECT COUNT(*) as count FROM house_numbers WHERE house_id = '$house_id'";
    $existing_house_numbers_result = mysqli_query($connection, $existing_house_numbers_query);
    $existing_house_numbers_data = mysqli_fetch_assoc($existing_house_numbers_result);
    $existing_house_numbers_count = $existing_house_numbers_data['count'];

    // Validate that the number of house numbers does not exceed the number of rooms
    if ($existing_house_numbers_count >= $numofrooms) {
        $error = "Cannot add more house numbers than the rentable units.";
    }

    $error = ''; // Reset error variable

    $sql_check = "SELECT * FROM house_numbers WHERE house_id = '$house_id' AND house_no = '$house_no'";
    $result_check = mysqli_query($connection, $sql_check);

    if (!$result_check) {
        die("Duplicate check query failed: " . mysqli_error($connection));
    }

    if (mysqli_num_rows($result_check) > 0) {
        $error = "House number already exists.";
    } elseif ($existing_house_numbers_count >= $numofrooms) {
        $error = "Cannot add more house numbers than the rentable units";

        $error = "Limit Reached.";
    } elseif (mysqli_num_rows($result_check) >= $numofrooms) {
        $error = "Cannot add more house numbers than the rentable units.";

        $error = "Cannot add more house numbers.";
    } else {
        // Insert the house number along with the status
        $sql = "INSERT INTO house_numbers (house_id, house_no) VALUES ('$house_id', '$house_no')";
        $query = mysqli_query($connection, $sql);

        if ($query) {
            // Debugging output
            echo "House number added successfully.";

            // Redirect to house-no.php with a success parameter

            // Debugging output
            echo "House number added successfully.";

            // Redirect to house-no.php with a success parameter
            header("Location: house-no.php?success=true");
            exit(); // Ensure no further code is executed after the redirect
        } else {
            $error = "There was an error while adding the house number. Please try again.";
        }
    }
}
?>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Hey there, <?php echo $username; ?></h4>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <ol class="breadcrumb">
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="houses.php">Houses</a></li>
                    <li class="active">New</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title m-b-0"><i class="fa fa-institution fa-3x"></i> Add A New House Number</h3>
                    <p class="text-muted m-b-30 font-13"> Fill in the form below: </p>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <?php echo $error; ?>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <label for="house_id">Property Name: *</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-home"></i></div>
                                    <select name="house_id" class="form-control" id="house_id" required>
                                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                            <option value="<?php echo $row['houseID']; ?>"><?php echo $row['house_name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="house_no">House Number: *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                                        <input type="text" required name="house_no" class="form-control" id="house_no" placeholder="Add House No" required="">
                                    </div>
                                </div>

        

                                <button type="submit" name="submit" class="btn btn-success btn-lg waves-effect waves-light m-r-10 center"><i class="fa fa-plus-circle fa-lg"></i> Add House Number</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Message on house-no.php -->

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
<!-- /.container-fluid -->
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
    
</script>
<!--END of local JS -->

</body>

</html>
<?php
// Send the output to the browser
ob_end_flush();
?>
