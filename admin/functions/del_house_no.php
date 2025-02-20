<?php 

require_once "db.php";

if (isset($_POST["id"])) {

    $id = $_POST["id"];

    $sql = "DELETE FROM `house_numbers` WHERE id=?"; // Assuming the table name is house_numbers

    $stmt = $db->prepare($sql);

    try {
        $stmt->execute([$id]);

        header('Location:../new-house-no.php?deleted');

    }  catch (Exception $e) {

        $e->getMessage();
        
        echo "Error";
    }
} else {
    header('Location:../new-house-no.php?del_error');
}
?>
