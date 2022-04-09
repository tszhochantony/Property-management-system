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
} else if ($_SESSION['user']['type'] != 'resident') {
    header('Location: index.php');
} else {
    $email = $_SESSION['user']['account'];
}
if (isSet($_POST['submit'])) {
    $feedback_details = $_POST['feedback_details'];
    $category_id = $_POST['category_id'];
    $ex_sql = false;
    $image_array = array();
    $name = "image";
    for($i = 1;$i<=3;$i++){
        $name = "image".$i;
        if($_FILES[$name]["name"]!=null){
            $ex_sql = true;
            $file_name = $_FILES[$name]['name'];
            $file_tmp = $_FILES[$name]['tmp_name'];
            array_push($image_array,$file_name);
            move_uploaded_file($file_tmp,"images/".$file_name);
        }
    }
     $image_str = json_encode($image_array);
     //insert into database
    $sql = $conn->prepare('SELECT COUNT(*) FROM feedback');
    $sql->execute();
    $result = $sql->get_result();
    $record = $result -> fetch_assoc();
    $record_id = $record['COUNT(*)']+1;
    if($ex_sql){
        $sql = $conn->prepare('INSERT INTO feedback (record_id, user_email, category_id, record_details, feedback_photo) VALUES (?, ?, ?, ?, ?)');
        $sql->bind_param('issss',$record_id, $email, $category_id, $feedback_details, $image_str);
    }else{
        $sql = $conn->prepare('INSERT INTO feedback (record_id, user_email, category_id, record_details) VALUES (?, ?, ?, ?)');
        $sql->bind_param('isss',$record_id, $email, $category_id, $feedback_details);
    }

    $sql->execute();
    if ($sql->affected_rows == 1) {
         $alert_msg = $send_success;
         $redirect = true;
    } else
    $alert_msg = $create_fail;
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
    <h1><?=$user_menuNew_feedback?></h1>
  </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
        <h2 style="color:#ffffff"><?=$report_feedback_detail?></h2>
        <table border="0" style="color:#ffffff">
            <tr>
                <td><?=$report_feedback_category?></td>
                <td>
                    <select class="" id="category_id" name="category_id">
                        <option value=""><?=$report_feedback_select?></option>
                        <?
                        $sql = $conn->prepare('SELECT * FROM feedback_category');
                        $sql->execute();
                        $result = $sql->get_result();
                        while ($record = $result -> fetch_assoc()) { ?>
                            <option value="<?=$record['category_id']?>"><?=$record['category_id'].' - '.$record['category_eng_name'].' '.$record['category_chi_name']?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?=$report_feedback_description?></td>
                <td><textarea name="feedback_details" rows="8" cols="80" placeholder="請詳細描述" required ></textarea></td>
            </tr>
            <tr>
                <td><?=$report_feedback_image?></td>
                <td>
                    <input type="file"  id="image1" name="image1" accept="image/*" capture="camera"/>
                    <input type="file"  id="image2" name="image2" accept="image/*" capture="camera"/>
                    <input type="file"  id="image3" name="image3" accept="image/*" capture="camera"/>
                </td>
            </tr>
        </table>
        <p> <input type="submit" name="submit" value="<?=$edit_resident_send?>" /> </p>
    </form>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) echo "window.location.replace('feedback_list.php');";
?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
    new gnMenu( document.getElementById( 'gn-menu' ) );
    $(document).ready(function(){
        $('#image2').hide();
        $('#image3').hide();
        $('#image1').change(function(event){
            if ($('#image1').get(0).files.length > 0) {
                $('#image2').show();
            }
        });
        $('#image2').change(function(event){
            if ($('#image2').get(0).files.length > 0) {
                $('#image3').show();
            }
        });
    });
</script>
</html>
<?$conn->close();?>
