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

$set_property = true;

$building_id = isSet($_POST['building_id']) ? $_POST['building_id'] : "";
$set_property = $set_property && $building_id != '';

$floor = isSet($_POST['floor']) ? $_POST['floor'] : "";
$set_property = $set_property && $floor != '';

$room_no = isSet($_POST['room_no']) ? $_POST['room_no'] : "";
$set_property = $set_property && $room_no != '';

$username = isSet($_POST['username']) ? $_POST['username'] : "";
$password = isSet($_POST['password']) ? hash('sha512', $_POST['password']) : "";
$confirm_password = isSet($_POST['confirm_password']) ? hash('sha512', $_POST['confirm_password']) : "";

if (isSet($_POST['submit'])) {
    $input_ok = true;
    $property_id = NULL;

    // validate password
    if ($password != $confirm_password) {
        $input_ok = false;
        $alert_msg = '錯誤：密碼不符！';
        $password = '';
        $confirm_password = '';
    }

    // validate username
    if ($input_ok) {
        $sql = $conn->prepare('SELECT * FROM resident WHERE username=?');
        $sql->bind_param('s', $username);

        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows > 0) {
            $alert_msg = '錯誤：此登入名稱已存在！';
            $input_ok = false;
            $username = '';
            $password = '';
            $confirm_password = '';
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
            $property_id = $record['row_id'];
        } else {
            $alert_msg = '錯誤：此單位資料不存在！';
            $input_ok = false;
            $password = '';
            $confirm_password = '';
        }
    }

    // insert into database
    if ($input_ok) {
        if ($chi_first_name == '') $chi_first_name = NULL;
        if ($chi_last_name == '') $chi_last_name = NULL;
        if ($email == '') $email = NULL;
        if ($mobile_phone == '') $mobile_phone = NULL;
        $sql = $conn->prepare('INSERT INTO resident (username, password, eng_first_name, eng_last_name, chi_first_name, chi_last_name, email, mobile_phone, property_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $sql->bind_param('ssssssssi', $username, $password, $eng_first_name, $eng_last_name, $chi_first_name, $chi_last_name, $email, $mobile_phone, $property_id);

        $sql->execute();
        if ($sql->affected_rows == 1) {
            $alert_msg = "新增成功！";
            $redirect = true;
        } else $alert_msg = "新增失敗！";
    }
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
    <h1>新 增 住 戶</h1>
  </header>
    <form class="" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <h2 style="color:#ffffff">住 戶 基 本 資 料</h2>
        <table border="0" style="color:#ffffff">
            <tr>
                <td>住戶英文姓氏：</td>
                <td><input type="text" name="eng_last_name" value="<?=$eng_last_name?>" maxlength="40" required /></td>
            </tr>
            <tr>
                <td>住戶英文名字：</td>
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
            <tr>
                <td>住戶電郵：</td>
                <td><input type="email" name="email" value="<?=$email?>" maxlength="128" /></td>
            </tr>
            <tr>
                <td>住戶可供接收信息的手機號碼：</td>
                <td><input type="text" name="mobile_phone" value="<?=$mobile_phone?>" maxlength="8" placeholder="+852" /></td>
            </tr>
        </table>
        <h2 style="color:#ffffff">居 住 資 料</h2>
        <table border="0" style="color:#ffffff">
            <tr>
                <td>居住樓宇：</td>
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
                <td>居住樓層：</td>
                <td>
                    <select class="" id="floor" name="floor">
                    </select>
                </td>
            </tr>
            <tr>
                <td>居住單位：</td>
                <td>
                    <select class="" id="room_no" name="room_no">
                    </select>
                </td>
            </tr>
        </table>
        <h2>登 入 資 料</h2>
        <table border="0">
            <tr>
                <td>登入名稱：</td>
                <td><input type="text" name="username" value="<?=$username?>" maxlength="128" required /></td>
            </tr>
            <tr>
                <td>密碼：</td>
                <td><input type="password" name="password" value="<?=$password?>" maxlength="128" required /></td>
            </tr>
            <tr>
                <td>確認密碼：</td>
                <td><input type="password" name="confirm_password" value="<?=$confirm_password?>" maxlength="128" required /></td>
            </tr>
        </table>
        <p> <input type="submit" name="submit" value="傳 送" /> </p>
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
