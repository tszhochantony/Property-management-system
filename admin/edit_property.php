<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;
$property_id = '';
$building_id = '';
$floor = '';
$room_no = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

if (isSet($_POST['submit'])) {
    $property_id = $_POST['property_id'];
    $building_id = $_POST['building_id'];
    $floor = $_POST['floor'];
    $room_no = $_POST['room_no'];

    $sql = $conn->prepare('SELECT * FROM property WHERE property_id<>? AND building_id=? AND floor=? AND room_no=?');
    $sql->bind_param('isss', $property_id, $building_id, $floor, $room_no);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "錯誤：此單位資料已存在！";
        else $alert_msg = "Error : This unit's data already exist !";
    } else {
        $sql = $conn->prepare('UPDATE property SET building_id=?, floor=?, room_no=? WHERE property_id=?');
        $sql->bind_param('sssi', $building_id, $floor, $room_no, $property_id);
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
    }

} else if (isSet($_GET['property_id'])) {
    $property_id = $_GET['property_id'];

    $sql = $conn->prepare('SELECT * FROM property WHERE property_id=?');
    $sql->bind_param('i', $property_id);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if ($record = $result->fetch_assoc()) {
            $building_id = $record['building_id'];
            $floor = $record['floor'];
            $room_no = $record['room_no'];
        }
    } else {
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "單位資料錯誤！";
        else $alert_msg = "Unit information Error！";
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
        <header><h1><?= $edit_property_title?></h1></header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="hidden" name="property_id" value="<?=$property_id?>" />
            <table border='0' style="color:#ffffff">
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
            <p> <input type="submit" name="submit" value="<?=$edit_property_modify?>" /> </p>
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
