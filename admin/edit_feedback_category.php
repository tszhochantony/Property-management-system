<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;
$category_id = '';
$category_chi_name = '';
$category_eng_name = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

if (isSet($_POST['submit'])) {
    $category_id = $_POST['category_id'];
    $category_chi_name = $_POST['category_chi_name'];
    $category_eng_name = $_POST['category_eng_name'];

    $sql = $conn->prepare('UPDATE feedback_category SET category_chi_name=?, category_eng_name=? WHERE category_id=?');
    $sql->bind_param('sss', $category_chi_name, $category_eng_name, $category_id);
    $sql->execute();

    if ($sql->affected_rows > 0) {
        $alert_msg = "更改成功！";
        $redirect = true;
    } else $alert_msg = "更改失敗！";
} else if (isSet($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    $sql = $conn->prepare('SELECT * FROM feedback_category WHERE category_id=?');
    $sql->bind_param('s', $category_id);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if ($record = $result->fetch_assoc()) {
            $category_chi_name = $record['category_chi_name'];
            $category_eng_name = $record['category_eng_name'];
        }
    } else {
        $alert_msg = '類別編號錯誤！';
        $redirect = true;
    }
} else {
    $alert_msg = '查詢資料庫時發生錯誤，請重試！';
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
    <h1><?=$edit_feedback_category_title?></h1>
  </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <input type="hidden" name="category_id" value="<?=$category_id?>" />
        <table border="0" style="color:#ffffff">
            <tr>
                <td><?=$create_feedback_category_id?></td>
                <td><?=$category_id?></td>
            </tr>
            <tr>
                <td><?=$create_feedback_category_chi?></td>
                <td><input type="text" name="category_chi_name" value="<?=$category_chi_name?>" maxlength="20" required /></td>
            </tr>
            <tr>
                <td><?=$create_feedback_category_eng?></td>
                <td><input type="text" name="category_eng_name" value="<?=$category_eng_name?>" maxlength="40" required /></td>
            </tr>
        </table>
        <p> <input type="submit" name="submit" value="<?=$edit_feedback_category_edit?>" /> </p>
    </form>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) echo "window.location.replace('feedback_category_management.php');";
?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
  new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
