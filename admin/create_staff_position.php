<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
}

$position_id = isSet($_POST['position_id']) ? $_POST['position_id'] : "";
$position_chi_name = isSet($_POST['position_chi_name']) ? $_POST['position_chi_name'] : "";
$position_eng_name = isSet($_POST['position_eng_name']) ? $_POST['position_eng_name'] : "";
$department_id = isSet($_POST['department_id']) ? $_POST['department_id'] : "";

if (isSet($_POST['submit'])) {
    $isPass = true;
    $sql = $conn->prepare('SELECT * FROM staff_position WHERE position_id=?');
    $sql->bind_param('s', $position_id);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $alert_msg = $create_position_error;
        $position_id = '';
        $isPass = false;
    }

    if($isPass){
        $sql = $conn->prepare('INSERT INTO staff_position (position_id, position_chi_name, position_eng_name, department_id) VALUES (?, ?, ?, ?)');
        $sql->bind_param('ssss', $position_id, $position_chi_name,  $position_eng_name, $department_id);

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
    <h1><?=$create_position_title?></h1>
  </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <table border="0" style="color:#ffffff">
            <tr>
                <td><?=$create_position_code?></td>
                <td><input type="text" name="position_id" value="<?=$position_id?>" maxlength="10" required /></td>
            </tr>
            <tr>
                <td><?=$create_position_zhName?></td>
                <td><input type="text" name="position_chi_name" value="<?=$position_chi_name?>" maxlength="20" required /></td>
            </tr>
            <tr>
                <td><?=$create_position_enName?></td>
                <td><input type="text" name="position_eng_name" value="<?=$position_eng_name?>" maxlength="40" required /></td>
            </tr>
            <tr>
                <td><?=$create_position_department?></td>
                <td>
                <select class="" id="department_id" name="department_id">
                        <?
                        $sql = $conn->prepare('SELECT department_id FROM department');
                        $sql->execute();
                        $result = $sql->get_result();
                        while ($record = $result -> fetch_assoc()) { ?>
                            <option value="<?=$record['department_id']?>"><?=$record['department_id']?></option>
                        <? } ?>
                </select>
                </td>
            </tr>
        </table>
        <p> <input type="submit" name="submit" value="<?=$create_building_create?>" /> </p>
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