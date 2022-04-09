<?
require('../common/conn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;

require '../lib/PHPMailer/src/Exception.php';
require '../lib/PHPMailer/src/PHPMailer.php';
require '../lib/PHPMailer/src/SMTP.php';
require '../lib/twilio-php-main/src/Twilio/autoload.php';

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;
$eng_last_name = isSet($_SESSION['input']) ? $_SESSION['input']['eng_last_name'] : '';
$eng_first_name = isSet($_SESSION['input']) ? $_SESSION['input']['eng_first_name'] : '';
$chi_last_name = isSet($_SESSION['input']) ? $_SESSION['input']['chi_last_name'] : '';
$chi_first_name = isSet($_SESSION['input']) ? $_SESSION['input']['chi_first_name'] : '';
$email = isSet($_SESSION['input']) ? $_SESSION['input']['email'] : '';
$mobile_phone = isSet($_SESSION['input']) ? $_SESSION['input']['mobile_phone'] : '';
$password = isSet($_SESSION['input']) ? $_SESSION['input']['password'] : '';
$otp = isSet($_SESSION['input']['otp']) ? $_SESSION['input']['otp'] : '';

if (isSet($_POST['submit'])) {
    $input_ok = true;

    if ($_POST['otp'] <> $otp) {
        if ($_SESSION['lang'] == 'zh') $alert_msg = '驗證碼錯誤，請重試！';
        else $alert_msg = 'Invalid verification code, please try again!';
        $input_ok = false;
    }

    if ($input_ok) {
        if ($chi_first_name == '') $chi_first_name = NULL;
        if ($chi_last_name == '') $chi_last_name = NULL;
        $sql = $conn->prepare('UPDATE resident SET status=1, password=?, eng_first_name=?, eng_last_name=?, chi_first_name=?, chi_last_name=?, mobile_phone=?, lang=? WHERE email=?');
        $sql->bind_param('ssssssss', $password, $eng_first_name, $eng_last_name, $chi_first_name, $chi_last_name, $mobile_phone, $_SESSION['lang'], $email);

        $sql->execute();
        if ($sql->affected_rows == 1) {
            sendEmail($email, $eng_first_name, $eng_last_name, $_SESSION['lang']);
            if ($_SESSION['lang'] == 'zh') {
                $alert_msg = "註冊成功！";
            } else $alert_msg = "Registration Success!";
            $redirect = true;
        } else {
            if ($_SESSION['lang'] == 'zh') {
                $alert_msg = "註冊失敗！";
            } else $alert_msg = "Registration Failed!";
        }
    }
} else if (!isSet($_SESSION['input'])) {
    if ($_SESSION['lang'] == 'zh') {
        $alert_msg = '未知的錯誤，請重試！';
    } else {
        $alert_msg = 'Unknown Error, please try again!';
    }
    $redirect = true;
} else {
    $_SESSION['input']['otp'] = generateOTP();
    sendSMS($mobile_phone, $_SESSION['input']['otp']);
    echo '<script>console.log('.$_SESSION['input']['otp'].');</script>';
}

function sendSMS($mobile_phone, $otp) {
    $mobile_phone = '+817040716715'; // 唔好郁呢個電話, 個API只會send SMS落呢個電話到
    $message = '';
    if ($_SESSION['lang'] == 'zh') {
        $message = '【WeProp】你的帳戶驗證碼為'.$otp.'。';
    } else {
        $message = '[WeProp]Your verification code is '.$otp.'.';
    }
    $account_sid = 'ACba95c1cc362d45b3794a2852568028bd';
    $auth_token = 'cefc3cb0eafa60cb498ac4df90eee16e';
    $twilio_number = "+14158438241";
    $client = new Client($account_sid, $auth_token);
    $client->messages->create(
        $mobile_phone,
        array(
            'from' => $twilio_number,
            'body' => $message
        )
    );
}

function sendEmail($email, $eng_first_name, $eng_last_name, $lang) {
    if ($lang == 'zh') {
        $email_context = '<p>親愛的 '.$eng_last_name.' '.$eng_first_name.'：</p>';
        $email_context .= '<p>再次歡迎閣下成為WeProp旗下屋苑的一份子，請登入以下連結以使用WeProp系統：</p>';
        $email_context .= '<a href="https://tomakizu.wtf/fyp/">https://tomakizu.wtf/fyp/</a>';
        $email_context .= '<p>WeProp團隊謹啟</p>';
    } else {
        $email_context = '<p>Dear '.$eng_last_name.' '.$eng_first_name.', </p>';
        $email_context .= '<p>Once again, welcome to WeProp\'s property. Please proceed to the following link for accessing the WeProp system: </p>';
        $email_context .= '<a href="https://tomakizu.wtf/fyp/">https://tomakizu.wtf/fyp/</a>';
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
    $mail->Subject = '歡迎加入WeProp / Welcome to WeProp';
    $mail->Body = $email_context;
    $mail->AddAddress($email);

    $mail->Send();
}

function generateOTP() {
    $result = '';
    for ($i = 0; $i < 6; $i++) {
        $result .= rand(0, 9);
    }

    return $result;
}
?>
<html>
<head>
    <?require("common/head.php");?>
</head>
<body>
  <div class="container">
    <?if ($_SESSION['lang'] == 'zh') {?>
        <header>
        <h1>住 戶 註 冊</h1>
        </header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="hidden" name="email" value="<?=$email?>" />
            <h2 style="color:#ffffff">手 機 認 證</h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td>驗證碼已發送到<?=substr($mobile_phone, 0, 4).' '.substr($mobile_phone, 4, 7)?>，請輸入信息內的驗證碼：</td>
                    <td> <input type="text" name="otp" value="" required /> </td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <p> <input type="submit" name="submit" value="確 認" /> </p>
        </form>
    <? } else {?>
        <header>
        <h1>住 戶 註 冊</h1>
        </header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="hidden" name="email" value="<?=$email?>" />
            <h2 style="color:#ffffff">Phone Verification</h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td>The verification code has been sent to <?=substr($mobile_phone, 0, 4).' '.substr($mobile_phone, 4, 7)?>, please enter the code sent to you:</td>
                    <td> <input type="text" name="otp" value="" required /> </td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <p> <input type="submit" name="submit" value="確 認" /> </p>
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
console.log('<?=$eng_last_name?>');
console.log('<?=$eng_first_name?>');
console.log('<?=$chi_last_name?>');
console.log('<?=$chi_first_name?>');
console.log('<?=$email?>');
console.log('<?=$mobile_phone?>');
console.log('<?=$password?>');
</script>
</html>
<?$conn->close();?>
