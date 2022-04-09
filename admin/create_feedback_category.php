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

$category_id = isSet($_POST['category_id']) ? $_POST['category_id'] : "";
$chi_category_name = isSet($_POST['chi_category_name']) ? $_POST['chi_category_name'] : "";
$eng_category_name = isSet($_POST['eng_category_name']) ? $_POST['eng_category_name'] : "";

if (isSet($_POST['submit'])) {
    $sql = $conn->prepare('SELECT * FROM feedback_category WHERE category_id=?');
    $sql->bind_param('s', $category_id);

    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $alert_msg = '錯誤：此問題類別編號已存在！';
        $category_id = '';
    } else {
        $sql = $conn->prepare('INSERT INTO feedback_category (category_id, category_chi_name, category_eng_name) VALUES (?, ?, ?)');
        $sql->bind_param('sss', $category_id, $chi_category_name, $eng_category_name);

        $sql->execute();
        if ($sql->affected_rows == 1) {
            $alert_msg = "新增成功！";
            $redirect = true;
        } else $alert_msg = "新增失敗！";
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
    <h1><?=$create_feedback_category_title?></h1>
  </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <table border="0" style="color:#ffffff">
            <tr>
                <td><?=$create_feedback_category_id?></td>
                <td><input type="text" name="category_id" value="<?=$category_id?>" maxlength="5" required /></td>
            </tr>
            <tr>
                <td><?=$create_feedback_category_chi?></td>
                <td><input type="text" name="chi_category_name" value="<?=$chi_category_name?>" maxlength="20" required /></td>
            </tr>
            <tr>
                <td><?=$create_feedback_category_eng?></td>
                <td><input type="text" name="eng_category_name" value="<?=$eng_category_name?>" maxlength="40" required /></td>
            </tr>
        </table>
        <p> <input type="submit" name="submit" value="<?=$create_feedback_category_add?>" /> </p>
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
