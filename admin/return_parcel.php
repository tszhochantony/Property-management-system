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
    if (isSet($_GET['id'])) {
        $sql = $conn->prepare('UPDATE parcel SET status=1 WHERE ref_num=?');
        $sql->bind_param('i', $_GET['id']);
        $sql->execute();
        if ($sql->affected_rows == 1) {
            $alert_msg = "更新成功！";
        } else $alert_msg = "更新失敗！";
    } else $alert_msg = '查詢資料庫時發生錯誤，請重試！';
}
?>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
?>
window.location.replace('hold_for_collection.php');
</script>
<?$conn->close();?>
