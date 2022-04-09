<?
require('../common/conn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../lib/PHPMailer/src/Exception.php';
require '../lib/PHPMailer/src/PHPMailer.php';
require '../lib/PHPMailer/src/SMTP.php';

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'resident') {
    header('Location: index.php');
}

$email = isSet($_POST['email']) ? $_POST['email'] : '';
$access_date = isSet($_POST['access_date']) ? $_POST['access_date'] : '';

if (isSet($_POST['submit'])) {
    $hash = hash('sha512', $email.$access_date);
    $sql = $conn->prepare('SELECT * FROM visitor WHERE hash=?');
    $sql->bind_param('s', $hash);
    $sql->execute();

    $result = $sql->get_result();
    if ($record = $result -> fetch_assoc()) {
        $alert_msg = "錯誤：閣下已邀請此電郵地址的持有人於 $access_date 進入大廈。";
        $email = '';
        $access_date = '';
    } else {
        $sql = $conn->prepare('INSERT INTO visitor (email, hash, resident_email, access_date) VALUES (?, ?, ?, ?)');
        $sql->bind_param('ssss', $email, $hash, $_SESSION['user']['account'], $access_date);

        $sql->execute();
        if ($sql->affected_rows == 1) {
            sendEmail($email, $hash, $_SESSION['user']['eng_name']);
            $alert_msg = '傳送成功！';
            $redirect = true;
        } else $alert_msg = "新增失敗！";
    }
}

function sendEmail($email, $hash, $inviter) {
    $email_context = '<p>親愛的 '.$email.'：</p>';
    $email_context .= '<p>系統收到由'.$inviter.'發出的訪客邀請要求。現誠邀閣下進入以下連結登記個人資料以獲得進入大廈的二維碼：</p>';
    $email_context .= '<a href="https://tomakizu.wtf/fyp/visitor/pre_register.php?lang=zh&id='.$hash.'">https://tomakizu.wtf/fyp/visitor/pre_register.php?id='.$hash.'</a>';
    $email_context .= '<p>如閣下並未得悉上述人士，請無須理會此則信息。</p>';
    $email_context .= '<p>WeProp團隊謹啟</p>';
    $email_context .= '<hr />';
    $email_context .= '<p>Dear '.$email.',</p>';
    $email_context .= '<p>We have recieved a visitor invitation request from '.$inviter.'. Please proceed to the following link for visitor registration: </p>';
    $email_context .= '<a href="https://tomakizu.wtf/fyp/visitor/pre_register.php?lang=en&id='.$hash.'">https://tomakizu.wtf/fyp/visitor/pre_register.php?id='.$hash.'</a>';
    $email_context .= '<p>If you do not recognize the above personnal, please ignore this email.</p>';
    $email_context .= '<p>Yours sincerely,</p>';
    $email_context .= '<p>The WeProp Team</p>';

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
    $mail->Subject = 'WeProp訪客邀請 / WeProp Visitor Invitation';
    $mail->Body = $email_context;
    $mail->AddAddress($email);

    $mail->Send();
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
    <h1><?=$invite_visitors?></h1>
    </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <table border=0 style="color:#ffffff">
            <tr>
                <th><?=$visitor_email?></th>
                <td> <input type="email" name="email" value="<?=$email?>" required /> </td>
            </tr>
            <tr>
                <th><?=$visit_date?></th>
                <td> <input type="date" name="access_date" value="<?=$access_date?>" min="<?=date('Y-m-d')?>" required /> </td>
            </tr>
        </table>
        <p> <input type="submit" name="submit" value="<?=$submit_invite?>" /> </p>
    </form>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
  new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
