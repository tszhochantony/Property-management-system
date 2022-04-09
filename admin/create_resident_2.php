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

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

$email = isSet($_POST['email']) ? $_POST['email'] : "";
$mobile_phone = isSet($_POST['mobile_phone']) ? $_POST['mobile_phone'] : "";

$set_property = true;

$building_id = isSet($_POST['building_id']) ? $_POST['building_id'] : "";
$set_property = $set_property && $building_id != '';

$floor = isSet($_POST['floor']) ? $_POST['floor'] : "";
$set_property = $set_property && $floor != '';

$room_no = isSet($_POST['room_no']) ? $_POST['room_no'] : "";
$set_property = $set_property && $room_no != '';

if (isSet($_POST['submit'])) {
    $input_ok = true;
    $property_id = NULL;

    // validate username
    if ($input_ok) {
        $sql = $conn->prepare('SELECT * FROM resident WHERE email=?');
        $sql->bind_param('s', $email);

        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows > 0) {
            if($_SESSION['lang'] == 'zh')
                $alert_msg = $create_resident_emailError;
            else $alert_msg = "Error : This email already exist !";
            $input_ok = false;
        }
    }

    // validate property
    if ($input_ok && $set_property) {
        $sql = $conn->prepare('SELECT * FROM property WHERE building_id=? AND floor=? AND room_no=?');
        $sql->bind_param('sss', $building_id, $floor, $room_no);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows == 1) {
            $record = $result -> fetch_assoc();
            $property_id = $record['property_id'];
        } else {
            if($_SESSION['lang'] == 'zh')
                $alert_msg = "錯誤：此單位資料不存在！";
            else $alert_msg = "Error : This unit's information does not exist !";
            $input_ok = false;
        }
    }

    // insert into database and send email
    if ($input_ok) {
        $hashed_email = hash('sha512', $email);
        if ($mobile_phone == '') $mobile_phone = NULL;
        $sql = $conn->prepare('INSERT INTO resident (email, hashed_email, mobile_phone, property_id) VALUES (?, ?, ?, ?)');
        $sql->bind_param('sssi', $email, $hashed_email, $mobile_phone, $property_id);

        $sql->execute();
        if ($sql->affected_rows == 1) {
            sendEmail($email, $hashed_email);
            if($_SESSION['lang'] == 'zh')
                $alert_msg = "新增成功！";
            else $alert_msg = "Create Sucessfully！";
            $redirect = true;
        } else{
            if($_SESSION['lang'] == 'zh')
                $alert_msg = "新增失敗！";
            else $alert_msg = "Create Failed！";
        }
    }
}

function sendEmail($email, $hashed_email) {
    $email_context = '<p>親愛的 '.$email.'：</p>';
    $email_context .= '<p>歡迎閣下成為WeProp旗下屋苑的一份子，請按以下連結完成註冊手續：</p>';
    $email_context .= '<a href="https://tomakizu.wtf/fyp/user/pre_register.php?lang=zh&id='.$hashed_email.'">https://tomakizu.wtf/fyp/user/pre_register.php?lang=zh&id='.$hashed_email.'</a>';
    $email_context .= '<p>如閣下並非WeProp旗下屋苑住戶，請無須理會此則信息。</p>';
    $email_context .= '<p>WeProp團隊謹啟</p>';
    $email_context .= '<hr />';
    $email_context .= '<p>Dear '.$email.',</p>';
    $email_context .= '<p>Welcome to WeProp\'s property. Please proceed to the following link for the account registration: </p>';
    $email_context .= '<a href="https://tomakizu.wtf/fyp/user/pre_register.php?lang=en&id='.$hashed_email.'">https://tomakizu.wtf/fyp/user/pre_register.php?lang=en&id='.$hashed_email.'</a>';
    $email_context .= '<p>If you are not the resident of WeProp\'s property, please ignore this email.</p>';
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
    $mail->Subject = 'WeProp住戶帳號申請 / WeProp Resident Account Registration';
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
    $(document).ready(function() {
        $('#building_id').on('change', function() {
            $('#floor').html("");
            $('#room_no').html("");
            var value = $(this).val();
            if (value.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "ajax/get_property_info.php?building_id=" + $(this).val(),
                    dataType: "json",
                    success: function(result) {
                        var options = '<option value="">請選擇...</option>';
                        for (var i = 0; i < result.length; i++) {
                            options += '<option value=' + result[i].floor + '>';
                            options += result[i].floor;
                            options += '</option>';
                        }
                        $('#floor').append(options);
                    },
                    error: function(err) {}
                });
            }
        });
        $('#floor').on('change', function() {
            $('#room_no').html("");
            var value = $(this).val();
            if (value.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "ajax/get_property_info.php?building_id=" + $('#building_id').val() + "&floor=" + $(this).val(),
                    dataType: "json",
                    success: function(result) {
                        var options = '<option value="">請選擇...</option>';
                        for (var i = 0; i < result.length; i++) {
                            options += '<option value=' + result[i].room_no + '>';
                            options += result[i].room_no;
                            options += '</option>';
                        }
                        $('#room_no').append(options);
                    },
                    error: function(err) {}
                });
            }
        });
    });
    </script>
</head>
<body>
    <?include('common/menuNew.php');?>
    <header>
    <h1><?=$create_resident_title?></h1>
  </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <h2 style="color:#ffffff"><?=$create_resident_basic?></h2>
        <table border="0" style="color:#ffffff">
            <tr>
                <td colspan="2"><?=$resident_have?><font color="red">*</font><?=$resident_require?></td>
            </tr>
            <tr>
                <td><?=$create_resident_email?><font color="red">*</font>：</td>
                <td><input type="email" name="email" value="<?=$email?>" maxlength="128" required /></td>
            </tr>
            <tr>
                <td><?=$create_resident_phone?></td>
                <td><input type="text" name="mobile_phone" value="<?=$mobile_phone?>" maxlength="8" placeholder="+852" /></td>
            </tr>
        </table>
        <h2 style="color:#ffffff"><?=$create_resident_address?></h2>
        <table border="0" style="color:#ffffff">
            <tr>
                <td><?=$create_resident_flat?></td>
                <td>
                    <select class="" id="building_id" name="building_id">
                        <option value="">請選擇...</option>
                        <?
                        $sql = $conn->prepare('SELECT * FROM building');
                        $sql->execute();
                        $result = $sql->get_result();
                        while ($record = $result -> fetch_assoc()) { ?>
                            <option value="<?=$record['building_id']?>"><?=$record['building_id'].' - '.$record['eng_building_name'].' '.$record['chi_building_name']?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?=$create_resident_floor?></td>
                <td>
                    <select class="" id="floor" name="floor">
                    </select>
                </td>
            </tr>
            <tr>
                <td><?=$create_resident_unit?></td>
                <td>
                    <select class="" id="room_no" name="room_no">
                    </select>
                </td>
            </tr>
        </table>
        <p> <input type="submit" name="submit" value="<?=$create_building_create?>" /> </p>
    </form>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) echo "window.location.replace('resident_management.php');";
?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
  new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
