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
$sql = $conn->prepare('SELECT * FROM staff WHERE staff_id=?');
    $sql->bind_param('s', $_SESSION['user']['account']);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows == 1) {
            $record = $result -> fetch_assoc();
            $staff_id = $_SESSION['user']['account'];
            $chi_first_name = $record['chi_first_name'];
            $chi_last_name = $record['chi_last_name'];
            $eng_first_name = $record['eng_first_name'];
            $eng_last_name = $record['eng_last_name'];
            $mobile_phone = $record['mobile_phone'];
            $password = $record['password'];
            $address = $record['address'];
        }

if (isSet($_POST['submit'])) {
    $input_ok = true;
    $change_password=false;
    $old_password = hash('sha512', $_POST['old_password']);
    $new_password = $_POST['new_password']!="" ? hash('sha512', $_POST['new_password']) : "";
    $new_password_confirm = $_POST['new_password']!="" ? hash('sha512', $_POST['new_password_confirm']) : "";
    $chi_first_name = $_POST['chi_first_name']!="" ? $_POST['chi_first_name'] : null;
    $chi_last_name = $_POST['chi_last_name']!="" ? $_POST['chi_last_name'] : null;
    $eng_first_name = $_POST['eng_first_name'];
    $eng_last_name = $_POST['eng_last_name'];
    $mobile_phone = $_POST['mobile_phone']!="" ? $_POST['mobile_phone'] : null;
    $address = $_POST['address']!="" ? $_POST['address'] : null;
    if($old_password!=$password){
        $input_ok = false;
        $alert_msg = '密碼錯誤，請重試！';
    }
    if($input_ok && $new_password==$password){
        $input_ok = false;
        $alert_msg = '新密碼不能與舊密碼相同，請重試！';
    }
    if($input_ok && $new_password!=$new_password_confirm){
        $input_ok = false;
        $alert_msg = '新密碼不一，請重試！';
    }
    if($new_password!=null){
       $change_password=true;
    }
    if ($input_ok) {
        if($change_password){
            $sql = $conn->prepare('UPDATE staff SET eng_last_name=?, eng_first_name=?, chi_last_name=?, chi_first_name=?, mobile_phone=?, password=?,address=? WHERE staff_id=?');
            $sql->bind_param('ssssssss', $eng_last_name, $eng_first_name, $chi_last_name, $chi_first_name, $mobile_phone,$new_password,$address, $staff_id);
        }else{
            $sql = $conn->prepare('UPDATE staff SET eng_last_name=?, eng_first_name=?, chi_last_name=?, chi_first_name=?, mobile_phone=?,address=? WHERE staff_id=?');
            $sql->bind_param('sssssss', $eng_last_name, $eng_first_name, $chi_last_name, $chi_first_name, $mobile_phone,$address, $staff_id);
        }
        $sql->execute();
        if ($sql->affected_rows == 1) {
            $alert_msg = "更新成功！";
            $redirect = true;
        } else $alert_msg = "更新失敗！*不能在沒有更改任何資料下更新";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?require("common/head.php");?>
        <script type="text/javascript">
        $(document).ready(function() {
        });
        </script>
    </head>
    <body>
        <?include('common/menuNew.php');?>
        <header><h1>更 改 個 人 資 料</h1></header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="hidden" name="email" value="<?=$email?>" />
            <h2 style="color:#ffffff"> 基 本 資 料</h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td>員工編號：</td>
                    <td><?=$staff_id?></td>
                </tr>
                <tr>
                    <td>英文姓氏：</td>
                    <td><input type="text" name="eng_last_name" value="<?=$eng_last_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td>英文名字：</td>
                    <td><input type="text" name="eng_first_name" value="<?=$eng_first_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td>中文姓氏：</td>
                    <td><input type="text" name="chi_last_name" value="<?=$chi_last_name?>" maxlength="6"/></td>
                </tr>
                <tr>
                    <td>中文名字：</td>
                    <td><input type="text" name="chi_first_name" value="<?=$chi_first_name?>" maxlength="6" /></td>
                </tr>
                <tr>
                    <td>手機號碼：</td>
                    <td><input type="text" name="mobile_phone" value="<?=$mobile_phone?>" maxlength="8" placeholder="+852"/></td>
                </tr>
                <tr>
                    <td>住址：</td>
                    <td><input type="text" name="address" value="<?=$address?>" /></td>
                </tr>
                <tr>
                    <td>新密碼：</td>
                    <td><input type="password" name="new_password" value="" maxlength="128"/></td>
                </tr>
                <tr>
                    <td>再次輸入新密碼：</td>
                    <td><input type="password" name="new_password_confirm" value=""  maxlength="128"/></td>
                </tr>
                <tr>
                    <td>舊密碼：</td>
                    <td><input type="password" name="old_password" value="" maxlength="128" required/></td>
                </tr>
            </table>
            <p> <input type="submit" name="submit" value="傳 送" /> </p>
        </form>
    </body>
    <script type="text/javascript">
    <?php
    if ($alert_msg <> '') echo "alert('$alert_msg');";
    if ($redirect) echo "window.location.replace('edit_personal_information.php');";
    ?>
    </script>
    <script src="../common/js/classie.js"></script>
    <script src="../common/js/gnmenu.js"></script>
    <script>
      new gnMenu( document.getElementById( 'gn-menu' ) );
    </script>
</html>
<?$conn->close();?>
