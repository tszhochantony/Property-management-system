<?
require('../common/conn.php');
session_start();
require_once('../lang/lang_conn.php');

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
} else {
    $alert_msg = '';
    if (isSet($_GET['id']) && isSet($_GET['status'])) {
        $sql = $conn->prepare('UPDATE staff SET status=? WHERE staff_id=?');
        $sql->bind_param('is', $_GET['status'], $_GET['id']);
        $sql->execute();
        if ($sql->affected_rows == 1) {
            if($_SESSION['lang'] == 'zh')
                $alert_msg = "更新成功！";
            else $alert_msg = "Change Sucessfully！";
        } else{
            if($_SESSION['lang'] == 'zh')
                $alert_msg = "更新失敗！";
            else $alert_msg = "Change Failed！"; 
        }
    }else{
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "查詢資料庫時發生錯誤，請重試！";
        else $alert_msg = "An error has occured when searching the database, please try again !"; 
    }
}
?>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
?>
window.location.replace('staff_management.php');
</script>
<?$conn->close();?>
