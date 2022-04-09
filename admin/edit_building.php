<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;
$building_id = '';
$chi_building_name = '';
$eng_building_name = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

if (isSet($_POST['submit'])) {
    $building_id = $_POST['building_id'];
    $chi_building_name = $_POST['chi_building_name'];
    $eng_building_name = $_POST['eng_building_name'];

    $sql = $conn->prepare('UPDATE building SET chi_building_name=?, eng_building_name=? WHERE building_id=?');
    $sql->bind_param('sss', $chi_building_name, $eng_building_name, $building_id);
    $sql->execute();

    if ($sql->affected_rows > 0) {
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "更改成功！";
        else $alert_msg = "Change Sucessfully！";                 
        $redirect = true;
    }else{
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "更改失敗！";
        else $alert_msg = "Change Failed！"; 
    }

} else if (isSet($_GET['building_id'])) {
    $building_id = $_GET['building_id'];

    $sql = $conn->prepare('SELECT * FROM building WHERE building_id=?');
    $sql->bind_param('s', $building_id);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if ($record = $result->fetch_assoc()) {
            $chi_building_name = $record['chi_building_name'];
            $eng_building_name = $record['eng_building_name'];
        }
    } else {
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "樓宇號碼錯誤！";
        else $alert_msg = "Wrong Building ID！";
        $redirect = true;
    }
} else {
    if($_SESSION['lang'] == 'zh')
            $alert_msg = "查詢資料庫時發生錯誤，請重試！";
    else $alert_msg = "An error has occured when searching the database, please try again !";
    $redirect = true;
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
    <h1><?= $edit_building_title?></h1>
  </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <input type="hidden" name="building_id" value="<?=$building_id?>" />
        <table border="0" style="color:#ffffff">
            <tr>
                <td><?=$edit_building_id ?></td>
                <td><?=$building_id?></td>
            </tr>
            <tr>
                <td><?=$edit_building_chinese?></td>
                <td><input type="text" name="chi_building_name" value="<?=$chi_building_name?>" maxlength="100" required /></td>
            </tr>
            <tr>
                <td><?=$edit_building_english?></td>
                <td><input type="text" name="eng_building_name" value="<?=$eng_building_name?>" maxlength="100" required /></td>
            </tr>
        </table>
        <p> <input type="submit" name="submit" value="<?= $edit_building_change ?>" /> </p>
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
