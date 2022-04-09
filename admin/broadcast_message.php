<?
require('../common/conn.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../lib/PHPMailer/src/Exception.php';
require '../lib/PHPMailer/src/PHPMailer.php';
require '../lib/PHPMailer/src/SMTP.php';

use Twilio\Rest\Client;
require '../lib/twilio-php-main/src/Twilio/autoload.php';

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;
$record_id = 0;
$building = isSet($_POST['building']) ? $_POST['building'] : array();
$owner = isSet($_POST['owner']) ? $_POST['owner'] : '';
$expire_date = isSet($_POST['expire_date']) ? $_POST['expire_date'] : '';
$email_title = isSet($_POST['email_title']) ? $_POST['email_title'] : '';
$email_context = isSet($_POST['email_context']) ? $_POST['email_context'] : '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

if (isSet($_POST['submit'])) {
    if (count($building) == 0 && $owner == '') {
        $alert_msg = $building_not_select;
    } else {
        $sql = $conn->prepare('INSERT INTO announcement_record (staff_id, expire_date, announcement_title, announcement_content) VALUES (?, ?, ?, ?)');
        $sql->bind_param('ssss', $_SESSION['user']['account'], $expire_date ,$email_title, $email_context);
        $sql->execute();

        $sql = $conn->prepare('SELECT * FROM announcement_record WHERE staff_id=? ORDER BY record_id DESC');
        $sql->bind_param('s', $_SESSION['user']['account']);
        $sql->execute();
        $result = $sql->get_result();
        while ($record = $result -> fetch_assoc()) {
            $record_id = $record['record_id'];
            break;
        }

        foreach ($building as $id) {
            $sql = $conn->prepare('INSERT INTO announcement_recipient (record_id, recipient) VALUES (?, ?)');
            $sql->bind_param('is', $record_id, $id);
            $sql->execute();
        }

        if ($owner <> '') {
            $sql = $conn->prepare('INSERT INTO announcement_recipient (record_id, recipient) VALUES (?, ?)');
            $sql->bind_param('is', $record_id, $owner);
            $sql->execute();
        }

        $sql = $conn->prepare('SELECT COUNT(*) AS count FROM building');
        $sql->execute();
        $result = $sql->get_result();
        if ($record = $result -> fetch_assoc()) {
            if ($record['count'] == count($building) && $owner <> '') { // all residents and owners
                $pre_sql = 'SELECT * FROM resident';
            } elseif ($record['count'] > 0 && $owner <> '') {           // all owners and selected residents
                $pre_sql = 'SELECT * FROM resident LEFT JOIN owner ON resident.email=owner.user_email LEFT JOIN property ON resident.property_id=property.property_id WHERE ';
                foreach ($building as $id) {
                    $pre_sql .= "property.building_id='".$id."' OR ";
                }
                $pre_sql .= 'owner.user_email IS NOT NULL';
            } elseif ($record['count'] > 0) {                           // only selected residents
                $pre_sql = 'SELECT * FROM resident LEFT JOIN property ON resident.property_id=property.property_id WHERE ';
                foreach ($building as $index=>$id) {
                    if ($index <> 0) $pre_sql .= "OR ";
                    $pre_sql .= "property.building_id='".$id."' ";
                }
            } else {                                                    // only owners
                $pre_sql = 'SELECT * FROM resident LEFT JOIN owner ON resident.email=owner.user_email WHERE owner.user_email IS NOT NULL';
            }
        }

        $sql = $conn->prepare($pre_sql);
        $sql->execute();
        $result = $sql->get_result();
        while ($record = $result -> fetch_assoc()) {
            sendSMS($record['mobile_phone'], $record['lang']);
            sendEmail($record['email'], $email_title, $email_context);
        }
        $alert_msg = $send_success;
        $email_title = '';
        $email_context = '';
        $redirect = true;
    }
}

function sendSMS($mobile_phone, $lang) {
    $mobile_phone = '+817040716715'; // 唔好郁呢個電話, 個API只會send SMS落呢個電話到
    $message = '';
    if ($lang == 'zh') {
        $message = '【WeProp】你收到了來自WeProp的信息，請前往WeProp住戶系統查看。';
    } else {
        $message = '[WeProp]You have received an annonuncement from WeProp, Please access the WeProp System to view the annonuncement.';
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

function sendEmail($email, $email_title, $email_context) {
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
}?>
<!DOCTYPE html>
<html>
<head>
    <?require("common/head.php");?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#select_all').click(function() {
                $('.building_checkbox').prop('checked', true);
            });
            $('#clear_all').click(function() {
                $('.building_checkbox').prop('checked', false);
            });
        });

        function update_context() {
            var quill_text = quill.root.innerHTML;
            document.getElementById('email_context').value = quill_text;
        }
    </script>
    <style media="screen">
        #editor p {
            text-align: left;
        }
    </style>
</head>

<body>
    <?include('common/menuNew.php');?>
    <header>
        <h1><?=$announcement_email_title?></h1>
    </header>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post" style="margin: 5% 5% 0 5%" onsubmit="update_context();">
                    <input type="hidden" name="email_context" id="email_context" value="<?=$email_context?>">
                    <table border="0">
                        <tr>
                            <td><?=$announcement_email_receiver?></td>
                            <td>
                                <button type="button" id="select_all">全選</button>&nbsp;&nbsp;
                                <button type="button" id="clear_all">清除</button><br />
                                <?
                                $sql = $conn->prepare('SELECT * FROM building ORDER BY building_id ASC');
                                $sql->execute();
                                $result = $sql->get_result();
                                while ($record = $result -> fetch_assoc()) {?>
                                    <input type="checkbox" name="building[]" class="building_checkbox" value="<?=$record['building_id']?>" checked="checked" /> <?=$record['chi_building_name'].' '.$record['eng_building_name']?>&nbsp;&nbsp;&nbsp;
                                <?}?>
                                <input type="checkbox" name="owner" class="building_checkbox" value="owner" checked="checked" />業主
                            </td>
                        </tr>
                        <tr>
                            <td><?=$announcement_expire_date?></td>
                            <td> <input type="date" name="expire_date" value="expire_date" required min="<?=date('Y-m-d')?>" /> </td>
                        </tr>
                        <tr>
                            <td><?=$announcement_email_topic?></td></br>
                            <td><input type="text" name="email_title" value="<?=$email_title?>" required size="70"/></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: text-top;"><?=$announcement_email_content?></td>
                            <td>
                                <div id="toolbar"></div>
                                <div id="editor"></div>
                                <script type="text/javascript">
                                    var toolbarOptions = [
                                        ['bold', 'italic', 'underline'],
                                        [{'color': []}, {'background': []}],
                                        ['link'],
                                        [{'list': 'bullet'}, {'list': 'ordered'}]
                                    ];
                                    var quill = new Quill('#editor', {
                                        modules: {
                                            toolbar: toolbarOptions
                                        },
                                        theme: 'snow'
                                    });
                                </script>
                            </td>
                        </tr>
                    </table>
                    <p style="text-align: right !important"> <input type="submit" name="submit" value="<?=$edit_resident_send?>" /> </p>
                </form>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) echo "window.location.replace('announcement_management.php');";
?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
