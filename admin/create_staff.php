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
$mobile_phone = isSet($_POST['mobile_phone']) ? $_POST['mobile_phone'] : "";
$address = isSet($_POST['address']) ? $_POST['address'] : "";

$department_id = isSet($_POST['department_id']) ? $_POST['department_id'] : "";
$position_id = isSet($_POST['position_id']) ? $_POST['position_id'] : "";

$staff_id = isSet($_POST['staff_id']) ? $_POST['staff_id'] : "";
$password = isSet($_POST['password']) ? hash('sha512', $_POST['password']) : "";
$confirm_password = isSet($_POST['confirm_password']) ? hash('sha512', $_POST['confirm_password']) : "";

if (isSet($_POST['submit'])) {
    $input_ok = true;

    // validate password
    if ($password != $confirm_password) {
        $input_ok = false;
        $alert_msg = $create_staff_wrongPw;
        $password = '';
        $confirm_password = '';
    }

    // validate staff_id
    if ($input_ok) {
        $sql = $conn->prepare('SELECT * FROM staff WHERE staff_id=?');
        $sql->bind_param('s', $staff_id);

        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows > 0) {
            $alert_msg = $create_staff_wrongId;
            $input_ok = false;
            $staff_id = '';
            $password = '';
            $confirm_password = '';
        }
    }

    // validate position
    if ($input_ok) {
        $sql = $conn->prepare('SELECT * FROM staff_position WHERE department_id=? AND position_id=?');
        $sql->bind_param('ss', $department_id, $position_id);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows == 0) {
            $alert_msg = $create_staff_wrongPosition;
            $input_ok = false;
            $password = '';
            $confirm_password = '';
        }
    }

    // insert into database
    if ($input_ok) {
        if ($chi_first_name == '') $chi_first_name = NULL;
        if ($chi_last_name == '') $chi_last_name = NULL;
        if ($mobile_phone == '') $mobile_phone = NULL;
        $sql = $conn->prepare('INSERT INTO staff (staff_id, password, eng_first_name, eng_last_name, chi_first_name, chi_last_name, mobile_phone, address, position_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $sql->bind_param('sssssssss', $staff_id, $password, $eng_first_name, $eng_last_name, $chi_first_name, $chi_last_name, $mobile_phone, $address, $position_id);

        $sql->execute();
        if ($sql->affected_rows == 1) {
            $alert_msg = $create_success;
            $redirect = true;
        } else $alert_msg = $create_fail;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?require("common/head.php");?>
        <script type="text/javascript">
        $(document).ready(function() {
            $('#department_id').on('change', function() {
                $('#position_id').html("");
                var value = $(this).val();
                if (value.length > 0) {
                    $.ajax({
                        type: "POST",
                        url: "ajax/get_position.php?department_id=" + $(this).val(),
                        dataType: "json",
                        success: function(result) {
                            var options = '<option value="">請選擇...</option>';
                            for (var i = 0; i < result.length; i++) {
                                options += '<option value=' + result[i].position_id;
                                options += result[i].position_id == '<?=$position_id?>' ? ' selected>' : '>';
                                options += result[i].position_id + ' - ' + result[i].position_eng_name + ' ' + result[i].position_chi_name;
                                options += '</option>';
                            }
                            $('#position_id').append(options);
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
            <h1><?=$create_staff_title?></h1>
        </header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <h2 style="color:#ffffff"><?=$edit_staff_basic?></h2>
            <table border='0' style="color:#ffffff">
                <tr>
                    <td><?=$edit_staff_enLast?></td>
                    <td><input type="text" name="eng_last_name" value="<?=$eng_last_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td><?=$edit_staff_enFirst?></td>
                    <td><input type="text" name="eng_first_name" value="<?=$eng_first_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td><?=$edit_staff_zhLast?></td>
                    <td><input type="text" name="chi_last_name" value="<?=$chi_last_name?>" maxlength="6" /></td>
                </tr>
                <tr>
                    <td><?=$edit_staff_zhFirst?></td>
                    <td><input type="text" name="chi_first_name" value="<?=$chi_first_name?>" maxlength="6" /></td>
                </tr>
                <tr>
                    <td><?=$edit_staff_phone?></td>
                    <td><input type="text" name="mobile_phone" value="<?=$mobile_phone?>" maxlength="8" placeholder="+852" /></td>
                </tr>
                <tr>
                    <td><?=$edit_staff_address?></td>
                    <td><input type="text" name="address" value="<?=$address?>" maxlength="255" /></td>
                </tr>
            </table>
            <h2 style="color:#ffffff"><?=$edit_staff_info?></h2>
            <table border='0' style="color:#ffffff">
                <tr>
                    <td><?=$edit_staff_department?></td>
                    <td>
                        <select class="" id="department_id" name="department_id">
                            <option value="">請選擇...</option>
                            <?
                            $sql = $conn->prepare('SELECT * FROM department');
                            $sql->execute();
                            $result = $sql->get_result();
                            while ($record = $result -> fetch_assoc()) { ?>
                                <option value="<?=$record['department_id']?>"<?=$record['department_id'] == $department_id ? " selected": ""?>><?=$record['department_id'].' - '.$record['department_eng_name'].' '.$record['department_chi_name']?></option>
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?=$edit_staff_position?></td>
                    <td>
                        <select class="" id="position_id" name="position_id">
                        </select>
                    </td>
                </tr>
            </table>
            <h2 style="color:#ffffff"><?=$create_staff_login?></h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td><?=$create_staff_loginId?></td>
                    <td><input type="text" name="staff_id" value="<?=$staff_id?>" maxlength="128" required /></td>
                </tr>
                <tr>
                    <td><?=$create_staff_loginPw?></td>
                    <td><input type="password" name="password" value="<?=$password?>" maxlength="128" required /></td>
                </tr>
                <tr>
                    <td><?=$create_staff_confirmPw?></td>
                    <td><input type="password" name="confirm_password" value="<?=$confirm_password?>" maxlength="128" required /></td>
                </tr>
            </table>
            <p> <input type="submit" name="submit" value="<?=$create_building_create?>" /> </p>
        </form>
    </body>
    <script type="text/javascript">
    <?php
    if ($alert_msg <> '') echo "alert('$alert_msg');";
    if ($redirect) echo "window.location.replace('staff_management.php');";
    ?>
    </script>
    <script src="../common/js/classie.js"></script>
    <script src="../common/js/gnmenu.js"></script>
    <script>
      new gnMenu( document.getElementById( 'gn-menu' ) );
    </script>
</html>
<?$conn->close();?>
