<?
require('../common/conn.php');
include('../lib/phpqrcode/qrlib.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../lib/PHPMailer/src/Exception.php';
require '../lib/PHPMailer/src/PHPMailer.php';
require '../lib/PHPMailer/src/SMTP.php';

session_start();
$alert_msg = '';
$redirect = false;

$email = isSet($_POST['email']) ? $_POST['email'] : '';
$hash = isSet($_POST['hash']) ? $_POST['hash'] : '';
$access_date = isSet($_POST['access_date']) ? $_POST['access_date'] : '';

$eng_last_name = isSet($_POST['eng_last_name']) ? strtoupper($_POST['eng_last_name']) : "";

if (isSet($_POST['eng_first_name'])) {
    $eng_first_name_array = explode(' ', $_POST['eng_first_name']);
    $eng_first_name = '';
    for ($i = 0; $i < count($eng_first_name_array); $i++) {
        $eng_first_name .= strtoupper($eng_first_name_array[$i][0]).substr($eng_first_name_array[$i], 1).' ';
    }
    $eng_first_name = substr($eng_first_name, 0, -1);
} else $eng_first_name = '';

$id_no_1 = isSet($_POST['id_no_1']) ? $_POST['id_no_1'] : '';
$id_no_2 = isSet($_POST['id_no_2']) ? $_POST['id_no_2'] : '';

if (isSet($_POST['submit'])) {
    $id_no = $id_no_1.''.$id_no_2;
    $sql = $conn->prepare('UPDATE visitor SET status=1, eng_first_name=?, eng_last_name=?, id_no=? WHERE hash=?');
    $sql->bind_param('ssss', $eng_first_name, $eng_last_name, $id_no, $hash);

    $sql->execute();
    if ($sql->affected_rows == 1) {
        QRcode::png($hash, 'temp/'.$hash.'.png');
        sendEmail($email, $hash, $eng_first_name, $eng_last_name, $_SESSION['lang'], $access_date);
        unlink('temp/'.$hash.'.png');   // delete generated QR code from server
        if ($_SESSION['lang'] == 'zh') {
            $alert_msg = "登記成功！";
        } else $alert_msg = "Registration Success!";
        $redirect = true;
    } else {
        if ($_SESSION['lang'] == 'zh') {
            $alert_msg = "登記失敗！";
        } else $alert_msg = "Registration Failed!";
    }
} else if (isSet($_GET['id'])) {
    $_SESSION['lang'] = isSet($_GET['lang']) ? $_GET['lang'] : 'zh';
    $hash = $_GET['id'];

    $sql = $conn->prepare('SELECT * FROM visitor WHERE hash=? AND status=2');
    $sql->bind_param('s', $hash);

    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if ($record = $result -> fetch_assoc()) {
            $email = $record['email'];
            $access_date = $record['access_date'];
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

function sendEmail($email, $hash, $eng_first_name, $eng_last_name, $lang, $access_date) {
    if ($lang == 'zh') {
        $email_context = '<p>親愛的 '.$eng_last_name.' '.$eng_first_name.'：</p>';
        $email_context .= '<p>系統已收到閣下提交的個人資料。閣下可於'.$access_date.'使用以下二維碼進入WeProp屋苑。</p>';
        $email_context .= '<img src="cid:qr_code" />';
        $email_context .= '<p>WeProp團隊謹啟</p>';
    } else {
        $email_context = '<p>Dear '.$eng_last_name.' '.$eng_first_name.', </p>';
        $email_context .= '<p>We have recieved your submitted personal information. You may use the following QR code to access the WeProp\'s Property on '.$access_date.'. </p>';
        $email_context .= '<img src="cid:qr_code" />';
        $email_context .= '<p>Yours sincerely, </p>';
        $email_context .= '<p>The WeProp Team</p>';
    }

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = '465';
    $mail->isHTML();
    $mail->Username = 'fyp.202107@gmail.com';
    $mail->Password = 'daisiirsdo';
    $mail->SetFrom('no-reply@weprop.com.hk', 'WeProp');
    $mail->Subject = 'WeProp訪客登記完成 / WeProp Visitor Registration Complete';
    $mail->Body = $email_context;
    $mail->AddEmbeddedImage('./temp/'.$hash.'.png', 'qr_code');
    $mail->AddAddress($email);

    $mail->Send();
}

?>
<html>
<head>
    <?require("common/head.php");?>
</head>
<body>
  <div class="container">
    <?if ($_SESSION['lang'] == 'zh') {?>
        <p class="language"><b style="color:#ffffff">中 | </b> <a href="pre_register.php?lang=en&id=<?=$hash?>">Eng</a></p>
        <header>
        <h1>訪 客 登 記</h1>
        </header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="hidden" name="email" value="<?=$email?>" />
            <input type="hidden" name="hash" value="<?=$hash?>" />
            <input type="hidden" name="access_date" value="<?=$access_date?>" />
            <table border="0" style="color:#ffffff">
                <tr>
                    <td colspan="2">有<font color="red">*</font>的項目為必填</td>
                </tr>
                <tr>
                    <td>訪客電郵：</td>
                    <td><?=$email?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>訪客到訪日期：</td>
                    <td><?=$access_date?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>訪客英文姓氏<font color="red">*</font>：</td>
                    <td><input type="text" name="eng_last_name" value="<?=$eng_last_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td>訪客英文名字<font color="red">*</font>：</td>
                    <td><input type="text" name="eng_first_name" value="<?=$eng_first_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td>身分證號碼字母及首四位數字<font color="red">*</font>：</td>
                    <td>
                        <input type="text" name="id_no_1" value="<?=$id_no_1?>" maxlength="2" size="1" required />
                        <input type="text" name="id_no_2" value="<?=$id_no_2?>" maxlength="4" size="4" required />
                    </td>
                </tr>
            </table>
            <p> <input type="submit" name="submit" value="傳 送" /> </p>
        </form>
    <? } else {?>
        <p class="language"><a href="pre_register.php?lang=zh&id=<?=$hash?>">中</a><b style="color:#ffffff"> | Eng</b></p>
        <header>
        <h1>Visitor Registration</h1>
       </header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="hidden" name="email" value="<?=$email?>" />
            <input type="hidden" name="hash" value="<?=$hash?>" />
            <input type="hidden" name="access_date" value="<?=$access_date?>" />
            <h2 style="color:#ffffff">Resident Basic Informaion</h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td>Visitor's Email: </td>
                    <td><?=$email?></td>
                </tr>
                <tr>
                    <td>Visiting Date：</td>
                    <td><?=$access_date?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Visitor's English Last Name: <font color="red">*</font></td>
                    <td><input type="text" name="eng_last_name" value="<?=$eng_last_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td>Visitor's English First Name: <font color="red">*</font></td>
                    <td><input type="text" name="eng_first_name" value="<?=$eng_first_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td>Identity Card Alphabets and First Four Digits: <font color="red">*</font></td>
                    <td>
                        <input type="text" name="id_no_1" value="<?=$id_no_1?>" maxlength="2" size="1" required />
                        <input type="text" name="id_no_2" value="<?=$id_no_2?>" maxlength="4" size="4" required />
                    </td>
                </tr>

            </table>
            <p> <input type="submit" name="submit" value="Submit" /> </p>
        </form>
    <?}?>
  </div>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) {
    echo "window.location.replace('index.php');";
    unset($_SESSION['lang']);
}
?>
</script>
</html>
<?$conn->close();?>
