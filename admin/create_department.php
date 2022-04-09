<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
}

$department_id = isSet($_POST['department_id']) ? $_POST['department_id'] : "";
$department_chi_name = isSet($_POST['department_chi_name']) ? $_POST['department_chi_name'] : "";
$department_eng_name = isSet($_POST['department_eng_name']) ? $_POST['department_eng_name'] : "";

if (isSet($_POST['submit'])) {
    $isPass = true;
    $sql = $conn->prepare('SELECT * FROM department WHERE department_id=?');
    $sql->bind_param('s', $department_id);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $alert_msg = $create_department_errorCode;
        $department_id = '';
        $isPass = false;
    }

    $sql = $conn->prepare('SELECT * FROM department WHERE department_chi_name=?');
    $sql->bind_param('s', $department_chi_name);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $alert_msg = $create_department_errorZhName;
        $department_chi_name = '';
        $isPass = false;
    }

    $sql = $conn->prepare('SELECT * FROM department WHERE department_eng_name=?');
    $sql->bind_param('s', $department_eng_name);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $alert_msg = $create_department_errorEnName;
        $department_eng_name = '';
        $isPass = false;
    }


    if($isPass){
        $sql = $conn->prepare('INSERT INTO department (department_id, department_chi_name, department_eng_name) VALUES (?, ?, ?)');
        $sql->bind_param('sss', $department_id, $department_chi_name,  $department_eng_name);

        $sql->execute();
        if ($sql->affected_rows == 1) {
            $alert_msg = $create_success;
            $redirect = true;
        } else $alert_msg = $create_fail;
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
    <h1><?=$create_department_title?></h1>
  </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <table border="0" style="color:#ffffff">
            <tr>
                <td><?=$department_management_code?></td>
                <td><input type="text" name="department_id" value="<?=$department_id?>" maxlength="10" required /></td>
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
