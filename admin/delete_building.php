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
    if (isSet($_GET['building_id'])) {
        $sql = $conn->prepare('DELETE FROM building WHERE building_id=?');
        $sql->bind_param('s', $_GET['building_id']);
        $sql->execute();
        if ($sql->affected_rows == 1) {
            if($_SESSION['lang'] == 'zh')
            $alert_msg = "刪除成功！";
            else $alert_msg = "Delete Sucessfully！";                 
        $redirect = true;
    }else{
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "刪除失敗！";
        else $alert_msg = "Delete Failed！";
    }
    } else {
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "查詢資料庫時發生錯誤，請重試！";
        else $alert_msg = "An error has occured when searching the database, please try again !";
    } 
}
?>
<script type="text/javascript">
<?
if ($alert_msg <> '') echo "alert('$alert_msg');";
?>
window.location.replace('building_management.php');
</script>
<?$conn->close();?>
