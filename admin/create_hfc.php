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

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

$eng_first_name = isSet($_POST['eng_first_name']) ? $_POST['eng_first_name'] : "";
$eng_last_name = isSet($_POST['eng_last_name']) ? $_POST['eng_last_name'] : "";

$mobile_phone = isSet($_POST['mobile_phone']) ? $_POST['mobile_phone'] : "";
$lang = isSet($_POST['lang']) ? $_POST['lang'] : "";
$resident_email = isSet($_POST['resident_email']) ? $_POST['resident_email'] : "";
$tracking_num = isSet($_POST['tracking_num']) ? $_POST['tracking_num'] : "";
$ref_num = date('ymdHis');
if (isSet($_POST['submit'])) {
    if ($tracking_num == '') $tracking_num = NULL;
    $sql = $conn->prepare('INSERT INTO parcel (resident_email, tracking_num, ref_num) VALUES (?, ?, ?)');
    $sql->bind_param('sss', $resident_email, $tracking_num, $ref_num);

    $sql->execute();
    if ($sql->affected_rows == 1) {
        $alert_msg = $create_success;
        sendSMS($mobile_phone, $tracking_num, $ref_num, $lang);
        sendEmail($resident_email, $eng_first_name, $eng_last_name, $lang, $tracking_num, $ref_num);
        $redirect = true;
    } else $alert_msg = $create_fail;
}

function sendSMS($mobile_phone, $tracking_num, $ref_num, $lang) {
    $mobile_phone = '+817040716715'; // 唔好郁呢個電話, 個API只會send SMS落呢個電話到
    $message = '';
    if ($lang == 'zh') {
        $message = '【WeProp】管理處已為閣下代收取一份';
        if ($tracking_num <> NULL) $message .= '追逐號碼為'.$tracking_num.'的';
        $message .= '包裹，請憑此信息及身份證明文件到管理處取回。【Ref:'.$ref_num.'】';
    } else {
        $message = '[WeProp]The management office has recieved a parcel ';
        if ($tracking_num <> NULL) $message .= '(Tracking Number: '.$tracking_num.') ';
        $message .= 'for you. Please proceed to the management office with this message and your identity credential to pick up your parcel.[Ref:'.$ref_num.']';
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

function sendEmail($email, $eng_first_name, $eng_last_name, $lang, $tracking_num, $ref_num) {
    if ($lang == 'zh') {
        $email_context = '<p>親愛的 '.$eng_last_name.' '.$eng_first_name.'：</p>';
        $email_context .= '<p>管理處已為閣下代收取一份';
        if ($tracking_num <> NULL) $email_context .= '追蹤號碼為'.$tracking_num.'的';
        $email_context .= '包裹，請儘快到管理處取回。</p>';
        $email_context .= '<p>WeProp團隊謹啟</p>';
    } else {
        $email_context = '<p>Dear '.$eng_last_name.' '.$eng_first_name.', </p>';
        $email_context .= '<p>The management office has recieved a parcel ';
        if ($tracking_num <> NULL) $email_context .= '(Tracking Number: '.$tracking_num.') ';
        $email_context .= 'for you. Please proceed to the management office to pick up your parcel as soon as possible.</p>';
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
    $mail->Subject = 'WeProp代收包裹通知 / WeProp Hold-for-Collection Notice (Ref: '.$ref_num.')';
    $mail->Body = $email_context;
    $mail->AddAddress($email);

    $mail->Send();
}
?>
<!DOCTYPE html>
<html>
<head>
    <?require("common/head.php");?>
    <script type="text/javascript">
        var submit_OK = false;
        function confirm_submit() {
            if (submit_OK) {
                return true;
            } else {
                alert("錯誤：無效的收件人！");
                return false;
            }
        }
        $(document).ready(function() {
            $('#ajax_result, #recipient_eng_name, #recipient_chi_name, #recipient_address').html("").hide();
            $('#search_resident').click(function() {
                submit_OK = false;
                $('#ajax_result, #recipient_eng_name, #recipient_chi_name, #recipient_address').html("").hide();
                $('#lang, #resident_email, #eng_last_name, #eng_first_name').val("");
                var table = '<?=$create_hfc_tb?>';
                $.ajax({
                    type: "POST",
                    url: "ajax/search_resident.php?mobile_phone=" + $('#mobile_phone').val(),
                    dataType: "json",
                    success: function(result) {
                        console.log(result);
                        table += '<table border=1 style="border-color: white;">';
                        table += '<tr><th><?=$create_hfc_tbEngName?></th><th><?=$create_hfc_tbChiName?></th><th><?=$create_hfc_tbEmail?></th><th><?=$create_hfc_tbAddress?></th><th></th></tr>';
                        for (var i = 0; i < result.length; i++) {
                            table += '<tr>';
                            table += '<td>' + result[i].eng_last_name + ', ' + result[i].eng_first_name + '</td>';
                            table += '<td>' + (result[i].chi_last_name == null || result[i].chi_first_name == null ? '查無資料' : result[i].chi_last_name + result[i].chi_first_name) + '</td>';
                            table += '<td>' + result[i].email + '</td>';
                            table += '<td>' + result[i].eng_building_name + ' ' + result[i].chi_building_name + ' ' + result[i].floor + '樓 ' + result[i].room_no + '室</td>';
                            table += '<td><button type="button" class="select-resident" value="' + result[i].email + '"><?=$create_hfc_select?></button></td>';
                            table += '</tr>';
                        }
                        table += '</table>';
                        $('#ajax_result').show().append(table);
                        $(".select-resident").click(function() {
                            submit_OK = true;
                            $('#resident_email').val($(this).val());
                            $.ajax({
                                type: "POST",
                                url: "ajax/query_resident_by_email.php?email=" + $(this).val(),
                                dataType: "json",
                                success: function(result) {
                                    $('#ajax_result').hide();
                                    $('#lang').val(result.lang);
                                    $('#eng_last_name').val(result.eng_last_name);
                                    $('#eng_first_name').val(result.eng_first_name);
                                    $('#recipient_eng_name').show().append("<td><?=$create_hfc_flatEng?></td><td>" + result.eng_last_name + ', ' + result.eng_first_name + "</td>");
                                    $('#recipient_chi_name').show().append("<td><?=$create_hfc_flatChi?></td><td>" + (result.chi_last_name == null || result.chi_first_name == null ? '查無資料' : result.chi_last_name + result.chi_first_name) + "</td>");
                                    $('#recipient_address').show().append("<td><?=$create_hfc_flatAddress?></td><td>" + result.eng_building_name + ' ' + result.chi_building_name + ' ' + result.floor + '樓 ' + result.room_no + "室</td>");
                                },
                                error: function(err) {}
                            });
                        });
                    },
                    error: function(err) {}
                });
            });
        });
    </script>
</head>
<body>
    <?include('common/menuNew.php');?>
  <header>
    <h1><?=$create_hfc_title?></h1>
  </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <input type="hidden" name="resident_email" id="resident_email" value="<?=$resident_email?>" />
        <input type="hidden" name="eng_last_name" id="eng_last_name" value="<?=$eng_last_name?>">
        <input type="hidden" name="eng_first_name" id="eng_first_name" value="<?=$eng_first_name?>">
        <input type="hidden" name="lang" id="lang" value="<?=$lang?>" />
        <table border="0" style="color:#ffffff">
            <tr>
                <td><?=$create_hfc_phone?></td>
                <td><input type="text" name="mobile_phone" id="mobile_phone" value="<?=$mobile_phone?>" minlength="8" maxlength="8" required /></td>
                <td> <button type="button" name="search_resident" id="search_resident"><?=$create_hfc_button?></button> <font color="red">*</font> </td>
            </tr>
            <tr id="select_resident">
                <td colspan="3" id="ajax_result"></td>
            </tr>
            <tr id="recipient_eng_name"></tr>
            <tr id="recipient_chi_name"></tr>
            <tr id="recipient_address"></tr>
            <tr>
                <td><?=$create_hfc_package?></td>
                <td><input type="text" name="tracking_num" value="<?=$tracking_num?>" maxlength="20" /></td>
            </tr>
        </table>
        <p> <input type="submit" name="submit" value="<?=$create_building_create?>" onclick="return confirm_submit();" /> </p>
    </form>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) echo "window.location.replace('hold_for_collection.php');";
?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
  new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
