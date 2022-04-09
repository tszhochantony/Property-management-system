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
    if (isSet($_GET['department_id'])) {
        $sql = $conn->prepare('SELECT * FROM staff_position WHERE department_id=?');
        $sql->bind_param('s', $_GET['department_id']);
        $sql->execute();
        $result = $sql->get_result();
        if (mysqli_num_rows($result) == 0) {

            $sql = $conn->prepare('DELETE FROM department WHERE department_id=?');
            $sql->bind_param('s', $_GET['department_id']);
            $sql->execute();
            if ($sql->affected_rows == 1) {
                $alert_msg = $update_success;
                $redirect = true;
            } else {
                $alert_msg = $update_fail;
                $redirect = true;
            } 

        } else{
            $alert_msg = $delete_department_error;
            $redirect = true;
        } 
    } 
}
?>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) 
    echo "window.location.replace('department_management.php');";

?>
</script>
<?$conn->close();?>