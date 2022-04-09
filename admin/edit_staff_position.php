<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;
$oldDepartment_id = '';
$position_id = '';
$position_chi_name = '';
$position_eng_name = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

if (isSet($_POST['submit'])) {
    $position_id = $_POST['position_id'];
    $position_chi_name = $_POST['position_chi_name'];
    $position_eng_name = $_POST['position_eng_name'];

        $sql = $conn->prepare('UPDATE staff_position SET position_chi_name=?, position_eng_name=? WHERE position_id=?');
        $sql->bind_param('sss', $position_chi_name, $position_eng_name, $position_id);
        $sql->execute();

        if ($sql->affected_rows > 0) {
            $alert_msg = $modify_success;
            $redirect = true;
        } else $alert_msg = $modify_fail;

} else if (isSet($_GET['position_id'])) {
    $position_id = $_GET['position_id'];
    $sql = $conn->prepare('SELECT * FROM staff_position WHERE position_id=?');
    $sql->bind_param('s', $position_id);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if ($record = $result->fetch_assoc()) {
            $position_id = $record['position_id'];
            $position_chi_name = $record['position_chi_name'];
            $position_eng_name = $record['position_eng_name'];
            $department_id = $record['department_id'];
        }
    } else {
        $alert_msg = $edit_staff_position_error;
        $redirect = true;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?require("common/head.php");?>
    </head>
    <body>
        <?include('common/menuNew.php');?>
        <header>
        <h1><?=$edit_position_title?></h1>
      </header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <table border='0' style="color:#ffffff">
                <tr>
                    <td><?=$position_management_positionCode?></td>
                        <td><input type="text" name="position_id" value="<?=$position_id?>" maxlength="10" required readonly/></td>
                </tr>
                <tr>
                    <td><?=$position_management_zhName?></td>
                    <td><input type="text" name="position_chi_name" value="<?=$position_chi_name?>" maxlength="20" required /></td>
                </tr>
                <tr>
                    <td><?=$position_management_enName?></td>
                    <td><input type="text" name="position_eng_name" value="<?=$position_eng_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td><?=$position_management_department?></td>
                    <td><input type="text" name="department_id" value="<?=$department_id?>" maxlength="40" readonly/></td>
                </tr>
            </table>
            <p> <input type="submit" name="submit" value="<?=$edit_property_modify?>" /> </p>
        </form>
    </body>
    <script type="text/javascript">
    <?php
    if ($alert_msg <> '') echo "alert('$alert_msg');";
    if ($redirect) echo "window.location.replace('staff_position_management.php');";
    ?>
    </script>
    <script src="../common/js/classie.js"></script>
    <script src="../common/js/gnmenu.js"></script>
    <script>
      new gnMenu( document.getElementById( 'gn-menu' ) );
    </script>
</html>
<?$conn->close();?>
