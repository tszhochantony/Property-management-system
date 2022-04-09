<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;
$oldDepartment_id = '';
$department_id = '';
$department_chi_name = '';
$department_eng_name = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

if (isSet($_POST['submit'])) {
    $department_id = $_POST['department_id'];
    $department_chi_name = $_POST['department_chi_name'];
    $department_eng_name = $_POST['department_eng_name'];

        $sql = $conn->prepare('UPDATE department SET department_chi_name=?, department_eng_name=? WHERE department_id=?');
        $sql->bind_param('sss', $department_chi_name, $department_eng_name, $department_id);
        $sql->execute();

        if ($sql->affected_rows > 0) {
            $alert_msg = $modify_success;
            $redirect = true;
        } else $alert_msg = $modify_fail;

} else if (isSet($_GET['department_id'])) {
    $oldDepartment_id = $_GET['department_id'];

    $sql = $conn->prepare('SELECT * FROM department WHERE department_id=?');
    $sql->bind_param('s', $oldDepartment_id);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if ($record = $result->fetch_assoc()) {
            $department_id = $record['department_id'];
            $department_chi_name = $record['department_chi_name'];
            $department_eng_name = $record['department_eng_name'];
        }
    } else {
        $alert_msg = $edit_department_error;
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
        <h1><?=$edit_department_title ?></h1>
      </header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <table border='0' style="color:#ffffff">
                <tr>
                    <td><?=$department_management_code?></td>
                        <td><input type="text" name="department_id" value="<?=$department_id?>" maxlength="10" required readonly/></td>
                </tr>
                <tr>
                    <td><?=$department_management_zhName?></td>
                    <td><input type="text" name="department_chi_name" value="<?=$department_chi_name?>" maxlength="20" required /></td>
                </tr>
                <tr>
                    <td><?=$department_management_enName?></td>
                    <td><input type="text" name="department_eng_name" value="<?=$department_eng_name?>" maxlength="40" required /></td>
                </tr>
            </table>
            <p> <input type="submit" name="submit" value="<?=$edit_property_modify?>" /> </p>
        </form>
    </body>
    <script type="text/javascript">
    <?php
    if ($alert_msg <> '') echo "alert('$alert_msg');";
    if ($redirect) echo "window.location.replace('department_management.php');";
    ?>
    </script>
    <script src="../common/js/classie.js"></script>
    <script src="../common/js/gnmenu.js"></script>
    <script>
      new gnMenu( document.getElementById( 'gn-menu' ) );
    </script>
</html>
<?$conn->close();?>
