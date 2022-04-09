<?
require('../common/conn.php');
session_start();
require_once('../lang/lang_conn.php');

$redirect = false;
if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
} else {
    $alert_msg = '';
    if (isSet($_GET['position_id'])) {

        $sql = $conn->prepare('SELECT * FROM staff WHERE position_id=?');
        $sql->bind_param('s', $_GET['position_id']);
        $sql->execute();
        $result = $sql->get_result();
        if (mysqli_num_rows($result) == 0) {

        $sql = $conn->prepare('DELETE FROM staff_position WHERE position_id=?');
        $sql->bind_param('s', $_GET['position_id']);
        $sql->execute();
        if ($sql->affected_rows == 1) {
            $alert_msg = $update_success;
            $redirect = true;
        } else {
            $alert_msg = $update_fail;
            $redirect = true;
        }

    } else {
        $alert_msg = $delete_staff_position_error;
        $redirect = true;
    } 
    }
}
?>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) 
    echo "window.location.replace('staff_position_management.php');";

?>
</script>
<?$conn->close();?>