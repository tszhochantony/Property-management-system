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

function sendEmail($selected_record_id,$main_sentence) {
  $sql = $GLOBALS['conn']->prepare('SELECT user_email FROM feedback WHERE record_id=?');
  $sql->bind_param('s', $selected_record_id);
  $sql->execute();
  $result = $sql->get_result();
  if ($record = $result -> fetch_assoc()) {
    $email = $record['user_email'];
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM resident WHERE email=?');
    $sql->bind_param('s', $email);
    $sql->execute();
    $result = $sql->get_result();
    if ($record = $result -> fetch_assoc()) {
      if ($_SESSION['lang'] == 'zh' && $record['chi_last_name']!=null && $record['chi_first_name']!=null) {
        $last_name = $record['chi_last_name'];
        $first_name = $record['chi_first_name'];
      } else {
        $last_name = $record['eng_last_name'];
        $first_name = $record['eng_first_name'];
      }
    }
  }
  if ($_SESSION['lang'] == 'zh') {
      $email_context = '<p>親愛的 '.$last_name.' '.$first_name.'：</p>';
      $email_context .= '<p>'.$main_sentence.'，請登入系統查看：</p>';
      $email_context .= '<a href="https://tomakizu.wtf/fyp">https://tomakizu.wtf/fyp</a>';
      $email_context .= '<p>WeProp團隊謹啟</p>';
  } else {
      $email_context = '<p>Dear '.$last_name.' '.$first_name.', </p>';
      $email_context .= '<p>'.$main_sentence.'. Please check the system to review the action taken: </p>';
      $email_context .= '<a href="https://tomakizu.wtf/fyp">https://tomakizu.wtf/fyp</a>';
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
  $mail->Subject = '有關你提出的問題 / Regarding your question';
  $mail->Body = $email_context;
  $mail->AddAddress($email);
  $mail->Send();
}

function sendSMS($main_sentence) {
  $mobile_phone = '+817040716715'; // 唔好郁呢個電話, 個API只會send SMS落呢個電話到
  $message = '';
  if ($_SESSION['lang'] == 'zh') {
      $message = '【WeProp】'.$main_sentence.'。';
  } else {
      $message = '[WeProp] '.$main_sentence.'.';
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
$href= $_POST['page'];
if(!isset($_SESSION['lang'])){
  $_SESSION['lang'] == 'zh';
  require_once('../lang/chinese.php');
}else if($_SESSION['lang'] == 'zh'){
  require_once('../lang/chinese.php');
}else if($_SESSION['lang'] == 'en'){
  require_once('../lang/english.php');
}
 if (!isSet($_SESSION['user'])) {
     echo "";
 } else if ($_SESSION['user']['type'] != 'staff') {
     echo "";
 } else {
    if (isSet($_POST['submit'])) {
        $success = true;
        $selected_record_id = $_POST['selected_record_id'];
        $action = $_POST['action'];
        if($action=='referral'){
          $department_id = $_POST['department_id'];
          $sql = $GLOBALS['conn']->prepare('UPDATE feedback SET status=1 WHERE record_id =? ');
          $sql->bind_param('i',$selected_record_id);
          $sql->execute();
          if ($sql->affected_rows == 1) {
            $success = true;
          }
          $sql = $GLOBALS['conn']->prepare('INSERT INTO feedback_referral (record_id, department_id) VALUES (?, ?)');
          $sql->bind_param('is',$selected_record_id, $department_id);
          $sql->execute();
          if ($sql->affected_rows == 1 && $success) {
              sendEmail($selected_record_id,$question_refered);
              $alert_msg = $refered_msg;
          } else {
            $alert_msg = $failed_msg;
          }
        }
        else if($action=='response'){
          $response = $_POST['response'];
          $percentage = $_POST['percentage'];
          $sql = $GLOBALS['conn']->prepare('INSERT INTO feedback_response (record_id, staff_id,response,percentage) VALUES (?, ?, ?, ?)');
          $sql->bind_param('issi',$selected_record_id, $_SESSION['user']['account'],$response,$percentage);
          $sql->execute();
          if ($sql->affected_rows == 1) {
            $total_percentage = 0;
            $sql = $GLOBALS['conn']->prepare('SELECT percentage FROM feedback_response WHERE record_id=?');
            $sql->bind_param('i', $selected_record_id);
            $sql->execute();
            $result = $sql->get_result();
            while ($record = $result -> fetch_assoc()) {
              $total_percentage += $record['percentage'];
            }
            if($total_percentage==100){
              $sql = $GLOBALS['conn']->prepare('UPDATE feedback SET status=2 WHERE record_id =? ');
              $sql->bind_param('i',$selected_record_id);
              $sql->execute();
              sendEmail($selected_record_id,$question_finish);
              sendSMS($question_finish);
            }else{
              sendEmail($selected_record_id,$question_updated);
              sendSMS($question_updated);
            }
              $alert_msg = $updated_msg;
          } else {
            $success = false;
          }
        }else{
          $success = false;
        }
        if(!$success){
          $alert_msg = $failed_msg;
        }
      }
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>message</title>
</head>
<body>

</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') {
  echo "alert('$alert_msg');";
}
?>
window.location.href="<?=$href?>";
</script>
</html>
