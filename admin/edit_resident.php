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

$set_property = true;

$building_id = isSet($_POST['building_id']) ? $_POST['building_id'] : "";
$set_property = $set_property && $building_id != '';

$floor = isSet($_POST['floor']) ? $_POST['floor'] : "";
$set_property = $set_property && $floor != '';

$room_no = isSet($_POST['room_no']) ? $_POST['room_no'] : "";
$set_property = $set_property && $room_no != '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

if (isSet($_POST['submit'])) {
    $input_ok = true;
    $property_id = NULL;

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
    // update database
    if ($input_ok) {
        if ($chi_first_name == '') $chi_first_name = NULL;
        if ($chi_last_name == '') $chi_last_name = NULL;
        if ($mobile_phone == '') $mobile_phone = NULL;
        // $sql = $conn->prepare('INSERT INTO resident (username, password, eng_first_name, eng_last_name, chi_first_name, chi_last_name, email, mobile_phone, property_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        // $sql->bind_param('ssssssssi', $username, $password, $eng_first_name, $eng_last_name, $chi_first_name, $chi_last_name, $email, $mobile_phone, $property_id);
        $sql = $conn->prepare('UPDATE resident SET eng_last_name=?, eng_first_name=?, chi_last_name=?, chi_first_name=?, mobile_phone=?, property_id=? WHERE email=?');
        $sql->bind_param('sssssis', $eng_last_name, $eng_first_name, $chi_last_name, $chi_first_name, $mobile_phone, $property_id, $email);

        $sql->execute();
        if ($sql->affected_rows == 1) {
            if($_SESSION['lang'] == 'zh')
                $alert_msg = "更改成功！";
            else $alert_msg = "Change Sucessfully！";
            $redirect = true;
        } else{
            if($_SESSION['lang'] == 'zh')
                $alert_msg = "更改失敗！";
            else $alert_msg = "Change Failed！"; 
        }
    }

} else if (isSet($_GET['email'])) {
    $email = $_GET['email'];

    $sql = $conn->prepare('SELECT * FROM resident WHERE email=?');
    $sql->bind_param('s', $email);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if ($record = $result->fetch_assoc()) {
            $status = $record['status'];
            $email = $record['email'];
            $eng_first_name = $record['eng_first_name'];
            $eng_last_name = $record['eng_last_name'];
            $chi_first_name = $record['chi_first_name'];
            $chi_last_name = $record['chi_last_name'];
            $mobile_phone = $record['mobile_phone'];

            if (!is_null($record['property_id'])) {
                $sql = $conn->prepare('SELECT * FROM property WHERE property_id=?');
                $sql->bind_param('i', $record['property_id']);
                $sql->execute();
                $result2 = $sql->get_result();

                if ($record2 = $result2->fetch_assoc()) {
                    $building_id = $record2['building_id'];
                    $floor = $record2['floor'];
                    $room_no = $record2['room_no'];
                }
            }
        }
    } else {
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "住戶資料錯誤！";
        else $alert_msg = "Resident information error！";
        $redirect = true;
    }
} else {
    if($_SESSION['lang'] == 'zh')
        $alert_msg = "查詢資料庫時發生錯誤，請重試！";
    else $alert_msg = "An error has occured when searching the database, please try again !";
    $redirect = true;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?require("common/head.php");?>
        <script type="text/javascript">
        $(document).ready(function() {
            var value = $('#building_id').val();
            if (value.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "ajax/get_property_info.php?building_id=" + value,
                    dataType: "json",
                    success: function(result) {
                        var options = '<option value="">請選擇...</option>';
                        for (var i = 0; i < result.length; i++) {
                            options += '<option value=' + result[i].floor;
                            options += result[i].floor == '<?=$floor?>' ? ' selected>' : '>';
                            options += result[i].floor;
                            options += '</option>';
                        }
                        $('#floor').append(options);
                        $.ajax({
                            type: "POST",
                            url: "ajax/get_property_info.php?building_id=" + $('#building_id').val() + "&floor=" + $('#floor').val(),
                            dataType: "json",
                            success: function(result) {
                                var options = '<option value="">請選擇...</option>';
                                for (var i = 0; i < result.length; i++) {
                                    options += '<option value=' + result[i].room_no;
                                    options += result[i].room_no == '<?=$room_no?>' ? ' selected>' : '>';
                                    options += result[i].room_no;
                                    options += '</option>';
                                }
                                $('#room_no').append(options);
                            },
                            error: function(err) {}
                        });
                    },
                    error: function(err) {}
                });
            }
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
        <header><h1><?=$edit_resident_title?></h1></header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="hidden" name="email" value="<?=$email?>" />
            <h2 style="color:#ffffff"><?=$edit_resident_basic?></h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td><?=$edit_resident_email?></td>
                    <td><?=$email?></td>
                </tr>
                <tr>
                    <td><?=$edit_resident_lastname?></td>
                    <td><input type="text" name="eng_last_name" value="<?=$eng_last_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td><?=$edit_resident_firstname?></td>
                    <td><input type="text" name="eng_first_name" value="<?=$eng_first_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td><?=$edit_resident_zhLast?></td>
                    <td><input type="text" name="chi_last_name" value="<?=$chi_last_name?>" maxlength="6" /></td>
                </tr>
                <tr>
                    <td><?=$edit_resident_zhName?></td>
                    <td><input type="text" name="chi_first_name" value="<?=$chi_first_name?>" maxlength="6" /></td>
                </tr>
                <tr>
                    <td><?=$edit_resident_phone?></td>
                    <td><input type="text" name="mobile_phone" value="<?=$mobile_phone?>" maxlength="8" placeholder="+852" /></td>
                </tr>
            </table>
            <h2 style="color:#ffffff"><?=$edit_resident_living?></h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td><?=$edit_resident_flat?></td>
                    <td>
                        <select class="" id="building_id" name="building_id">
                            <option value="">請選擇...</option>
                            <?
                            $sql = $conn->prepare('SELECT * FROM building');
                            $sql->execute();
                            $result = $sql->get_result();
                            while ($record = $result -> fetch_assoc()) { ?>
                                <option value="<?=$record['building_id']?>"<?=$record['building_id'] == $building_id ? " selected" : ""?>><?=$record['building_id'].' - '.$record['eng_building_name'].' '.$record['chi_building_name']?></option>
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?=$edit_resident_floor?></td>
                    <td>
                        <select class="" id="floor" name="floor">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?=$edit_resident_unit?></td>
                    <td>
                        <select class="" id="room_no" name="room_no">
                        </select>
                    </td>
                </tr>
            </table>
            <p> <input type="submit" name="submit" value="<?=$edit_resident_send?>" /> </p>
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
