<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;

$building_id = isSet($_POST['building_id']) ? $_POST['building_id'] : "";
$floor = isSet($_POST['floor']) ? $_POST['floor'] : "";
$room_no = isSet($_POST['room_no']) ? $_POST['room_no'] : "";

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

if (isSet($_POST['submit'])) {
    $sql = $conn->prepare('SELECT * FROM property WHERE building_id=? AND floor=? AND room_no=?');
    $sql->bind_param('sss', $building_id, $floor, $room_no);

    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "錯誤：此單位資料已存在！";
        else $alert_msg = "Error : This unit's data already exist !";
        $building_id = '';
        $floor = '';
        $room_no = '';
    } else {
        $sql = $conn->prepare('INSERT INTO property (building_id, floor, room_no) VALUES (?, ?, ?)');
        $sql->bind_param('sss', $building_id, $floor, $room_no);

        $sql->execute();
        if ($sql->affected_rows == 1) {
            $property_id = '';
            $sql = $conn->prepare('SELECT * FROM property WHERE building_id=? AND floor=? AND room_no=?');
            $sql->bind_param('sss', $building_id, $floor, $room_no);
            $sql->execute();
            $result = $sql->get_result();
            if ($record = $result->fetch_assoc()) {
                $property_id = $record['property_id'];
            }

            $sql = $conn->prepare('INSERT INTO owner (property_id) VALUES (?)');
            $sql->bind_param('i', $property_id);
            $sql->execute();
            if ($sql->affected_rows == 1) {
                if($_SESSION['lang'] == 'zh')
                    $alert_msg = "新增成功！";
                else $alert_msg = "Create Sucessfully！";
                $redirect = true;
            }else{
                if($_SESSION['lang'] == 'zh')
                    $alert_msg = "新增失敗！";
                else $alert_msg = "Create Failed！"; 
            }
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
    <h1><?=$create_property_title?></h1>
  </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <table border="0" style="color:#ffffff">
            <tr>
                <td><?=$edit_property_flat?></td>
                <td>
                    <select name="building_id">
                        <?
                        $sql = $conn->prepare('SELECT * FROM building');
                        $sql->execute();
                        $result = $sql->get_result();
                        while ($record = $result -> fetch_assoc()) { ?>
                            <option value="<?=$record['building_id']?>"<?=$building_id == $record['building_id'] ? " selected" : ""?>><?=$record['building_id'].' - '.$record['eng_building_name'].' '.$record['chi_building_name']?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?=$edit_property_floor?></td>
                <td><input type="text" name="floor" value="<?=$floor?>" maxlength="5" required /></td>
            </tr>
            <tr>
                <td><?=$edit_property_number?></td>
                <td><input type="text" name="room_no" value="<?=$room_no?>" maxlength="5" required /></td>
            </tr>
        </table>
        <p> <input type="submit" name="submit" value="<?=$create_building_create?>" /> </p>
    </form>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) echo "window.location.replace('property_management.php');";
?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
  new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
