<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pgnm="Nyumbani : View Houses Numbers";
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
    #allow access
    // Modify the SQL query to select house_name and house status
    $sql = "SELECT house_numbers.id, houses.house_name, house_numbers.house_no
            FROM house_numbers
            INNER JOIN houses ON house_numbers.house_id = houses.houseID";
    $query = mysqli_query($connection, $sql);

    /*******************************************************
                    Introduce the admin header
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
                <h4 class="page-title"><?php echo $username; ?></h4>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <ol class="breadcrumb">
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="#" class="active">Houses</a></li>
                    <li><a href="new-house-no.php">New</a></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">

                    <?php
                    // Display success message if redirected from new-house-no.php
                    if (isset($_GET["success"]) && $_GET["success"] == "true") {
                        echo '<div class="alert alert-success">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
                                <strong>SUCCESS!! </strong><p> The new house number has been added successfully.</p>
                              </div>';
                    }

                    // Display error messages if any
                    echo $error;

                    if (isset($_GET["deleted"])) {
                        echo '<div class="alert alert-warning">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
                                <strong>DELETED!! </strong><p> The house number has been successfully deleted.</p>
                              </div>';
                    } elseif (isset($_GET["del_error"])) {
                        echo '<div class="alert alert-danger">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
                                <strong>ERROR!! </strong><p> There was an error during deleting this record. Please try again.</p>
                              </div>';
                    }
                    ?>

                    <h3 class="box-title m-b-0">Current House Number Listings (<x style="color: orange;"><?php echo mysqli_num_rows($query); ?></x>)</h3>
                    <p class="text-muted m-b-30">Export data to Copy, CSV, Excel, PDF & Print</p>
                    <div class="table-responsive">
                        <table id="example23" class="display nowrap" cellspacing="0" width="100%">
                            <?php
                            if (mysqli_num_rows($query) == 0) {
                                echo "<i style='color:brown;'>No house numbers to display :( </i> ";
                            } else {
                                echo '
                                <thead>
                                    <tr>
                                        <th>House Name</th>
                                        <th>House No</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>House Name</th>
                                        <th>House No</th>
                                        <th>Actions</th>
                                    </tr>
                                </tfoot>
                                <tbody>';
                            }

                            while ($row = mysqli_fetch_array($query)) {
                                echo '
                                <tr>
                                    <td>' . $row["house_name"] . '</td>
                                    <td>' . $row["house_no"] . '</td>
                                    <td>
                                        <a href="#">
                                            <i class="fa fa-trash" data-toggle="modal" data-target="#responsive-modal' . $row["id"] . '" title="remove" style="color:red;"></i>
                                           <i class="fa fa-pencil" data-toggle="modal" data-target="#update-modal' . $row["id"]  . '"title="Update" style="color:blue;"></i>

                                        </a>
                                    </td>
                                    <!-- Modal for deletion confirmation -->
                                    <div id="responsive-modal' . $row["id"] . '" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title">Are you really sure you want to delete ' . $row["house_no"] . '\'s house record?</h4>
                                                    <h5>This action could detach all tenant records linked to this house.</h5>
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="functions/del_house_no.php" method="post">
                                                        <input type="hidden" name="id" value="' . $row["id"] . '"/>
                                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger waves-effect waves-light">Delete anyway</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Modal -->

                                    <!-- Modal for updating house number -->
                                    <div id="update-modal' . $row["id"] . '" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title">Update House Number</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="functions/update-house.php" method="post">
                                                        <div class="form-group">
                                                            <label for="house_id">House Name</label>
                                                            <input type="text" class="form-control" id="house_id" name="house_id" value="' . $row["house_name"] . '" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="house_no">House Number</label>
                                                            <input type="text" class="form-control" id="house_no" name="house_no" value="' . $row["house_no"] . '">
                                                            </div>
                                                            <input type="hidden" name="id" value="' . $row["id"] . '"/>
                                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-info waves-effect waves-light">Update</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                            </div>
                                        </div>
                                    </div>
                                </tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
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
    <?php require "admin_footer.php"; ?>
    <script>
        $(document).ready(function () {
            $('#example23').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
    <!--Style Switcher -->
    <script src="../plugins/bower_components/styleswitcher/jQuery.style.switcher.js"></script>

</body>
</html>
<?php
    }
ob_end_flush();
?>

