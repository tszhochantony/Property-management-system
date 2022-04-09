<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;

$eng_last_name = isSet($_POST['eng_last_name']) ? strtoupper($_POST['eng_last_name']) : "";

if (isSet($_POST['eng_first_name'])) {
    $eng_first_name_array = explode(' ', $_POST['eng_first_name']);
    $eng_first_name = '';
    for ($i = 0; $i < count($eng_first_name_array); $i++) {
        $eng_first_name .= strtoupper($eng_first_name_array[$i][0]).substr($eng_first_name_array[$i], 1).' ';
    }
    $eng_first_name = substr($eng_first_name, 0, -1);
} else $eng_first_name = '';

$chi_last_name = isSet($_POST['chi_last_name']) ? $_POST['chi_last_name'] : "";
$chi_first_name = isSet($_POST['chi_first_name']) ? $_POST['chi_first_name'] : "";
$email = isSet($_POST['email']) ? $_POST['email'] : "";
$mobile_phone = isSet($_POST['mobile_phone']) ? $_POST['mobile_phone'] : "";

$password = isSet($_POST['password']) ? hash('sha512', $_POST['password']) : "";
$confirm_password = isSet($_POST['confirm_password']) ? hash('sha512', $_POST['confirm_password']) : "";

if (isSet($_POST['submit'])) {
    $input_ok = true;

    // validate password
    if ($password != $confirm_password) {
        $input_ok = false;
        if ($_SESSION['lang'] == 'zh') {
            $alert_msg = '錯誤：密碼不符！';
        } else $alert_msg = 'Error: Password does not match!';
        $password = '';
        $confirm_password = '';
    }
    // insert into database
    if ($input_ok) {
        $_SESSION['input']['eng_last_name'] = $eng_last_name;
        $_SESSION['input']['eng_first_name'] = $eng_first_name;
        $_SESSION['input']['chi_last_name'] = $chi_last_name;
        $_SESSION['input']['chi_first_name'] = $chi_first_name;
        $_SESSION['input']['email'] = $email;
        $_SESSION['input']['mobile_phone'] = $mobile_phone;
        $_SESSION['input']['password'] = $password;
        header('Location: pre_register_2.php');
    }
} else if (isSet($_GET['id'])) {
    $_SESSION['lang'] = isSet($_GET['lang']) ? $_GET['lang'] : 'zh';
    $hashed_email = $_GET['id'];

    $sql = $conn->prepare('SELECT * FROM resident WHERE hashed_email=? AND status=2');
    $sql->bind_param('s', $hashed_email);

    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if ($record = $result -> fetch_assoc()) {
            $email = $record['email'];
            $mobile_phone = $record['mobile_phone'];
        }
    } else {
        if ($_SESSION['lang'] == 'zh') {
            $alert_msg = '錯誤：此網址已失效！';
        } else $alert_msg = "Error: This link is no longer valid!";
        $redirect = true;
    }
} else {
    if ($_SESSION['lang'] == 'zh') {
        $alert_msg = '錯誤：此網址已失效！';
    } else $alert_msg = "Error: This link is no longer valid!";
    $redirect = true;
}
?>
<html>
<head>
    <?require("common/head.php");?>
</head>
<body>
  <div class="container">
    <?if ($_SESSION['lang'] == 'zh') {?>
        <p class="language"><b style="color:#ffffff">中 | </b> <a href="pre_register.php?lang=en&id=<?=$hashed_email?>">Eng</a></p>
        <header>
        <h1>住 戶 註 冊</h1>
        </header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="hidden" name="email" value="<?=$email?>" />
            <h2 style="color:#ffffff">住 戶 基 本 資 料</h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td colspan="2">有<font color="red">*</font>的項目為必填</td>
                </tr>
                <tr>
                    <td>住戶電郵：</td>
                    <td><?=$email?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>住戶可供接收信息的手機號碼<font color="red">*</font>：</td>
                    <td><input type="text" name="mobile_phone" value="<?=$mobile_phone?>" maxlength="8" placeholder="+852" required /></td>
                </tr>
                <tr>
                    <td>住戶英文姓氏<font color="red">*</font>：</td>
                    <td><input type="text" name="eng_last_name" value="<?=$eng_last_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td>住戶英文名字<font color="red">*</font>：</td>
                    <td><input type="text" name="eng_first_name" value="<?=$eng_first_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td>住戶中文姓氏：</td>
                    <td><input type="text" name="chi_last_name" value="<?=$chi_last_name?>" maxlength="6" /></td>
                </tr>
                <tr>
                    <td>住戶中文名字：</td>
                    <td><input type="text" name="chi_first_name" value="<?=$chi_first_name?>" maxlength="6" /></td>
                </tr>
            </table>
            <h2 style="color:#ffffff">登 入 資 料</h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td colspan="2">有<font color="red">*</font>的項目為必填</td>
                </tr>
                <tr>
                    <td>密碼<font color="red">*</font>：</td>
                    <td><input type="password" name="password" value="<?=$password?>" maxlength="128" required /></td>
                </tr>
                <tr>
                    <td>確認密碼<font color="red">*</font>：</td>
                    <td><input type="password" name="confirm_password" value="<?=$confirm_password?>" maxlength="128" required /></td>
                </tr>
            </table>
            <p> <input type="submit" name="submit" value="註 冊" /> </p>
        </form>
    <? } else {?>
        <p class="language"><a href="pre_register.php?lang=zh&id=<?=$hashed_email?>">中</a><b style="color:#ffffff"> | Eng</b></p>
        <header>
        <h1>Resident Registration</h1>
       </header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="hidden" name="email" value="<?=$email?>" />
            <h2 style="color:#ffffff">Resident Basic Informaion</h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td>Resident Email: </td>
                    <td><?=$email?></td>
                </tr>
                <tr>
                    <td>Resident's Phone Number with SMS Receiving Function: </td>
                    <td><input type="text" name="mobile_phone" value="<?=$mobile_phone?>" minlength="8" maxlength="8" placeholder="+852" required /></td>
                </tr>
                <tr>
                    <td>Resident's English Last Name: </td>
                    <td><input type="text" name="eng_last_name" value="<?=$eng_last_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td>Resident's English First Name: </td>
                    <td><input type="text" name="eng_first_name" value="<?=$eng_first_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td>Resident's Chinese Last Name (if applicable): </td>
                    <td><input type="text" name="chi_last_name" value="<?=$chi_last_name?>" maxlength="6" /></td>
                </tr>
                <tr>
                    <td>Resident's Chinese First Name (if applicable): </td>
                    <td><input type="text" name="chi_first_name" value="<?=$chi_first_name?>" maxlength="6" /></td>
                </tr>
            </table>
            <h2 style="color:#ffffff">Login Information</h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td>Password: </td>
                    <td><input type="password" name="password" value="<?=$password?>" maxlength="128" required /></td>
                </tr>
                <tr>
                    <td>Confirm Password: </td>
                    <td><input type="password" name="confirm_password" value="<?=$confirm_password?>" maxlength="128" required /></td>
                </tr>
            </table>
            <p> <input type="submit" name="submit" value="Register" /> </p>
        </form>
    <?}?>
  </div>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) {
    echo "window.location.replace('index.php');";
}
?>
</script>
</html>
<?$conn->close();?>
