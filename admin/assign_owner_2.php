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
    if (isSet($_GET['property_id']) && isSet($_GET['email'])) {
        $sql = $conn->prepare('UPDATE owner SET user_email=? WHERE property_id=?');
        $sql->bind_param('si', $_GET['email'], $_GET['property_id']);
        $sql->execute();
        if ($sql->affected_rows == 1) {
            if($_SESSION['lang'] == 'zh')
            $alert_msg = "更改成功！";
            else $alert_msg = "Change Sucessfully！";
            $redirect = true;
        } else{
            if($_SESSION['lang'] == 'zh')
                $alert_msg = "更改失敗！";
            else $alert_msg = "Change Failed！"; 
        }
    } else if (isSet($_GET['property_id']) && isSet($_GET['mode'])) {
        if ($_GET['mode'] == 'clear') {
            $sql = $conn->prepare('UPDATE owner SET user_email=NULL WHERE property_id=?');
            $sql->bind_param('i', $_GET['property_id']);
            $sql->execute();
            if ($sql->affected_rows == 1) {
                if($_SESSION['lang'] == 'zh')
                    $alert_msg = "更改成功！";
                else $alert_msg = "Change Sucessfully！";
                $redirect = true;
            } else{
                if($_SESSION['lang'] == 'zh')
                    $alert_msg = "更改失敗！";
                else $alert_msg = "Change Failed！"; 
            }
        } else{
            if($_SESSION['lang'] == 'zh')
                $alert_msg = "查詢資料庫時發生錯誤，請重試！";
            else $alert_msg = "An error has occured when searching the database, please try again !"; 
        }
    } else{
            if($_SESSION['lang'] == 'zh')
                $alert_msg = "查詢資料庫時發生錯誤，請重試！";
            else $alert_msg = "An error has occured when searching the database, please try again !"; 
        }
}
?>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) {
    echo "window.location.replace('property_management.php');";
} else echo "window.location.replace('assign_owner_1.php?property_id={$_GET['property_id']}');";
?>
</script>
<?$conn->close();?>
