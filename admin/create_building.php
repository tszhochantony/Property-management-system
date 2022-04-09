<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

$building_id = isSet($_POST['building_id']) ? $_POST['building_id'] : "";
$chi_building_name = isSet($_POST['chi_building_name']) ? $_POST['chi_building_name'] : "";
$eng_building_name = isSet($_POST['eng_building_name']) ? $_POST['eng_building_name'] : "";

if (isSet($_POST['submit'])) {
    $sql = $conn->prepare('SELECT * FROM building WHERE building_id=?');
    $sql->bind_param('s', $building_id);

    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if($_SESSION['lang'] == 'zh')
                $alert_msg = "錯誤：此樓宇號碼已存在！";
        else $alert_msg = "Error : this building id already exist !";                 
        $building_id = '';
    } else {
        $sql = $conn->prepare('INSERT INTO building (building_id, chi_building_name, eng_building_name) VALUES (?, ?, ?)');
        $sql->bind_param('sss', $building_id, $chi_building_name, $eng_building_name);

        $sql->execute();
        if ($sql->affected_rows == 1) {
            if($_SESSION['lang'] == 'zh')
            $alert_msg = "新增成功！";
        else $alert_msg = "Sucessfully Added！";                 
        $redirect = true;
    }else{
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "新增失敗！";
        else $alert_msg = "Create Failed！"; 
    }
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
    <h1><?= $create_building_title ?></h1>
  </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <table border="0" style="color:#ffffff">
            <tr>
                <td><?= $edit_building_id ?></td>
                <td><input type="text" name="building_id" value="<?=$building_id?>" maxlength="20" required /></td>
            </tr>
            <tr>
                <td><?= $edit_building_chinese ?></td>
                <td><input type="text" name="chi_building_name" value="<?=$chi_building_name?>" maxlength="100" required /></td>
            </tr>
            <tr>
                <td><?= $edit_building_english ?></td>
                <td><input type="text" name="eng_building_name" value="<?=$eng_building_name?>" maxlength="100" required /></td>
            </tr>
        </table>
        <p> <input type="submit" name="submit" value="<?= $create_building_create ?>" /> </p>
    </form>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) echo "window.location.replace('building_management.php');";
?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
  new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
