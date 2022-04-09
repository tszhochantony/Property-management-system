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
$redirect = false;

$name = '';
$email = '';
$hashed_email = isSet($_POST['hashed_email']) ? $_POST['hashed_email'] : "";;

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

  // reset and update Password
  if ($input_ok) {
    $sql = $conn->prepare('UPDATE resident set password=NULL, status=0 WHERE hashed_email=?');
    $sql->bind_param('s', $hashed_email);
    $sql->execute();

    $sql = $conn->prepare('UPDATE resident set password=?, status=1 WHERE hashed_email=?');
    $sql->bind_param('ss', $password, $hashed_email);
    $sql->execute();
    if ($sql->affected_rows == 1) {
      $sql = $conn->prepare('SELECT * FROM resident WHERE hashed_email=?');
      $sql->bind_param('s', $hashed_email);
      $sql->execute();
      $result = $sql->get_result();

      if ($record = $result -> fetch_assoc()) {
        $email = $record['email'];
        if ($_SESSION['lang'] == 'zh') {
          $name = $record['chi_last_name'].' '.$record['chi_first_name'];
        } else {
          $name = $record['eng_last_name'].' '.$record['eng_first_name'];
        }
      }

      sendEmail($email, $name, $_SESSION['lang']);

      if ($_SESSION['lang'] == 'zh') {
        $alert_msg = "重設成功！";
      } else $alert_msg = "Reset Success!";
      $redirect = true;
    } else {
      if ($_SESSION['lang'] == 'zh') {
        $alert_msg = "重設失敗！";
      } else $alert_msg = "Reset Failed!";
      $password = '';
      $confirm_password = '';
    }

  }
} else if (isSet($_GET['id'])) {
  $hashed_email = $_GET['id'];

  $sql = $conn->prepare('SELECT * FROM resident WHERE hashed_email=? AND status=1');
  $sql->bind_param('s', $hashed_email);
  $sql->execute();
  $result = $sql->get_result();

  if ($result->num_rows > 0) {
    if ($record = $result -> fetch_assoc()) {
      $_SESSION['lang'] = $record['lang'];
    }
  } else {
    $alert_msg = "錯誤：此網址已失效！\nError: This link is no longer valid!";
    $redirect = true;
  }
} else {
  $alert_msg = "錯誤：此網址已失效！\nError: This link is no longer valid!";
  $redirect = true;
}

function sendEmail($email, $name, $lang) {
  $email_title = '';
  $email_context = '';
  if ($lang == 'zh') {
    $email_title = '重設WeProp住戶密碼成功';
    $email_context .= '<p>親愛的 '.$name.'：</p>';
    $email_context .= '<p>閣下最近更新了WeProp住戶帳號的密碼。若這是閣下執行的操作，則不需要採取進一步動作。</p>';
    $email_context .= '<p>若閣下「未」執行這項密碼變更動作，請立即造訪<a href="http://tomakizu.wtf:4913">WeProp主頁</a>重設帳戶密碼。</p>';
    $email_context .= '<p>WeProp團隊謹啟</p>';
  } else {
    $email_title = 'WeProp Resident Account\'s Password Change Successful';
    $email_context .= '<p>Dear '.$name.'：</p>';
    $email_context .= '<p>This is confirmation that your password for your WeProp resident account associated with this email address has been changed.</p>';
    $email_context .= '<p>If you did not personally request this change, please securely reset your password through <a href="http://tomakizu.wtf:4913">WeProp Main Page</a>.</p>';
    $email_context .= '<p>Yours sincerely,</p>';
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
  $mail->Subject = $email_title;
  $mail->Body = $email_context;
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
      <header>
      <h1>重 設 密 碼</h1>
      </header>
      <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>?id=<?=$hashed_email?>" method="post">
        <input type="hidden" name="hashed_email" value="<?=$hashed_email?>" />
        <table border="0" style="color:#ffffff">
          <tr>
            <td>密碼：</td>
            <td><input type="password" name="password" value="<?=$password?>" maxlength="128" required /></td>
          </tr>
          <tr>
            <td>確認密碼：</td>
            <td><input type="password" name="confirm_password" value="<?=$confirm_password?>" maxlength="128" required /></td>
          </tr>
        </table>
        <p> <input type="submit" name="submit" value="重 設" /> </p>
      </form>
    <? } else {?>
      <header>
      <h1>Reset Password</h1>
      </header>
      <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <input type="hidden" name="hashed_email" value="<?=$hashed_email?>" />
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
        <p> <input type="submit" name="submit" value="Reset" /> </p>
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
<?
$conn->close();
?>
