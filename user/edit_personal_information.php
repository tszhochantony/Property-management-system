<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'resident') {
    header('Location: index.php');
}
$sql = $conn->prepare('SELECT * FROM resident WHERE email=?');
    $sql->bind_param('s', $_SESSION['user']['account']);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows == 1) {
            $record = $result -> fetch_assoc();
            $email = $_SESSION['user']['account'];
            $chi_first_name = $record['chi_first_name'];
            $chi_last_name = $record['chi_last_name'];
            $eng_first_name = $record['eng_first_name'];
            $eng_last_name = $record['eng_last_name'];
            $mobile_phone = $record['mobile_phone'];
            $password = $record['password'];
        }

if (isSet($_POST['submit'])) {
    $input_ok = true;
    $change_password=false;
    $old_password = hash('sha512', $_POST['old_password']);
    $new_password = $_POST['new_password']!="" ? hash('sha512', $_POST['new_password']) : null;
    $new_password_confirm = $_POST['new_password']!="" ? hash('sha512', $_POST['new_password_confirm']) : null;
    $chi_first_name = $_POST['chi_first_name']!="" ? $_POST['chi_first_name'] : null;
    $chi_last_name = $_POST['chi_last_name']!="" ? $_POST['chi_last_name'] : null;
    $eng_first_name = $_POST['eng_first_name'];
    $eng_last_name = $_POST['eng_last_name'];
    $mobile_phone = $_POST['mobile_phone'];
    if($old_password!=$password){
        $input_ok = false;
        $alert_msg = $personal_wrongPw;
    }
    if($input_ok && $new_password==$password){
        $input_ok = false;
        $alert_msg = $personal_samePw;
    }
    if($input_ok && $new_password!=$new_password_confirm){
        $input_ok = false;
        $alert_msg = $personal_notEqualPw;
    }
    if($new_password!=null){
       $change_password=true;
    }
    if ($input_ok) {
        if($change_password){
            $sql = $conn->prepare('UPDATE resident SET eng_last_name=?, eng_first_name=?, chi_last_name=?, chi_first_name=?, mobile_phone=?, password=? WHERE email=?');
            $sql->bind_param('sssssss', $eng_last_name, $eng_first_name, $chi_last_name, $chi_first_name, $mobile_phone,$new_password, $email);
        }else{
            $sql = $conn->prepare('UPDATE resident SET eng_last_name=?, eng_first_name=?, chi_last_name=?, chi_first_name=?, mobile_phone=? WHERE email=?');
            $sql->bind_param('ssssss', $eng_last_name, $eng_first_name, $chi_last_name, $chi_first_name, $mobile_phone, $email);
        }
        $sql->execute();
        if ($sql->affected_rows == 1) {
            $alert_msg = $update_success;
            $redirect = true;
        } else $alert_msg = $personal_lessPw;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?require("common/head.php");?>
        <script type="text/javascript">
        $(document).ready(function() {
        });
        </script>
    </head>
    <body>
        <?include('common/menuNew.php');?>
        <header><h1><?=$edit_personal_info_title?></h1></header>
        <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="hidden" name="email" value="<?=$email?>" />
            <h2 style="color:#ffffff"><?=$edit_personal_info_basic?></h2>
            <table border="0" style="color:#ffffff">
                <tr>
                    <td><?=$edit_personal_info_email?></td>
                    <td><?=$email?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?=$edit_personal_info_enLast?></td>
                    <td><input type="text" name="eng_last_name" value="<?=$eng_last_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td><?=$edit_personal_info_enFirst?></td>
                    <td><input type="text" name="eng_first_name" value="<?=$eng_first_name?>" maxlength="40" required /></td>
                </tr>
                <tr>
                    <td><?=$edit_personal_info_zhLast?></td>
                    <td><input type="text" name="chi_last_name" value="<?=$chi_last_name?>" maxlength="6"/></td>
                </tr>
                <tr>
                    <td><?=$edit_personal_info_zhFirst?></td>
                    <td><input type="text" name="chi_first_name" value="<?=$chi_first_name?>" maxlength="6" /></td>
                </tr>
                <tr>
                    <td><?=$edit_personal_info_phone?></td>
                    <td><input type="text" name="mobile_phone" value="<?=$mobile_phone?>" maxlength="8" placeholder="+852" required/></td>
                </tr>
                <tr>
                    <td><?=$edit_personal_info_newPw?></td>
                    <td><input type="password" name="new_password" value="" maxlength="128"/></td>
                </tr>
                <tr>
                    <td><?=$edit_personal_info_reenter?></td>
                    <td><input type="password" name="new_password_confirm" value=""  maxlength="128"/></td>
                </tr>
                <tr>
                    <td><?=$edit_personal_info_oldPw?></td>
                    <td><input type="password" name="old_password" value="" maxlength="128" required/></td>
                </tr>
            </table>
            <p> <input type="submit" name="submit" value="<?=$edit_personal_info_send?>" /> </p>
        </form>
    </body>
    <script type="text/javascript">
    <?php
    if ($alert_msg <> '') echo "alert('$alert_msg');";
    if ($redirect) echo "window.location.replace('edit_personal_information.php');";
    ?>
    </script>
    <script src="../common/js/classie.js"></script>
    <script src="../common/js/gnmenu.js"></script>
    <script>
      new gnMenu( document.getElementById( 'gn-menu' ) );
    </script>
</html>
<?$conn->close();?>
