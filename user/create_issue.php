<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$redirect = false;
$input_ok = true;

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'resident') {
    header('Location: index.php');
} else if ($_SESSION['user']['is_owner'] != '1') {
    header('Location: main.php');
}

$issue_title = isSet($_POST['issue_title']) ? $_POST['issue_title'] : "";
$issue_details = isSet($_POST['issue_details']) ? $_POST['issue_details'] : "";
$cutoff_date = isSet($_POST['cutoff_date']) ? $_POST['cutoff_date'] : "";
$options = isSet($_POST['option']) ? $_POST['option'] : array();

if (isSet($_POST['submit'])) {
    for ($i = 0; $i < count($options); $i++) {
        for ($j = $i+1; $j < count($options); $j++) {
            if ($options[$i] == $options[$j]) {
                $alert_msg = '錯誤：出現重複選項！';
                $input_ok = false;
                $i = count($options);
                $j = count($options);
            }
        }
    }

    if ($input_ok) {
        $sql = $conn->prepare('INSERT INTO issue (issue_title, issue_details, raise_flag, raised_by, cutoff_date) VALUES (?, ?, 0, ?, ?)');
        $sql->bind_param('ssss', $issue_title, $issue_details, $_SESSION['user']['account'], $cutoff_date);
        $sql->execute();
        if ($sql->affected_rows == 1) {
            $issue_id = 0;
            $sql = $conn->prepare('SELECT * FROM issue WHERE raised_by=? ORDER BY issue_id DESC');
            $sql->bind_param('s', $_SESSION['user']['account']);
            $sql->execute();
            $result2 = $sql->get_result();
            while ($record2 = $result2 -> fetch_assoc()) {
                $issue_id = intval($record2['issue_id']);
                //echo '<script>alert("'.$issue_id.'");</script>';
                break;
            }
            for ($i = 0; $i < count($options); $i++) {
                $cid = "A$i";
                $sql = $conn->prepare('INSERT INTO issue_choice (issue_id, choice_id, choice_chi_desc, choice_eng_desc) VALUES (?, ?, ?, "N/A")');
                $sql->bind_param('iss', $issue_id, $cid, $options[$i]);
                $sql->execute();
            }
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
            var option_count = 2;
            $('#add_option').click(function() {
                //$('#option' + option_count).hide();
                option_count++;
                var tr = '';
                tr += '<tr id="option' + option_count + '">';
                tr += '<td>選項：</td>';
                tr += '<td><input type="text" name="option[]" required maxlength="50" /></td>';
                tr += '<td><button type="button" name="delete_option" class="delete_option" id="option' + option_count + '" value="option' + option_count + '">刪除選項</button></td>';
                tr += '</tr>'
                $('#option_table').append(tr);
                $('.delete_option').click(function() {
                    $('#' + $(this).val()).remove();
                });
            });
        });
    </script>
</head>
<body>
    <?include('common/menuNew.php');?>
  <header>
    <h1>新 增 議 程</h1>
  </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <h2 style="color:white;">議程資料</h2>
        <table border="0" style="color:#ffffff">
            <tr>
                <td>議程標題：</td>
                <td><input type="text" name="issue_title" value="<?=$issue_title?>" maxlength="128" required /></td>
            </tr>
            <tr>
                <td>議程內容：</td>
                <td><textarea name="issue_details" rows="8" cols="80" required maxlength="1024"><?=$issue_details?></textarea></td>
            </tr>
            <tr>
                <td>投票完結時間：</td>
                <td><input type="date" name="cutoff_date" value="<?=$cutoff_date?>" required min="<?=date('Y-m-').(date('d')+1)?>" /></td>
            </tr>
        </table>
        <h2 style="color:white;">投票選項</h2>
        <table border="0" style="color:#ffffff" id="option_table">
            <tr>
                <td>選項：</td>
                <td><input type="text" name="option[]" required maxlength="50" /></td>
            </tr>
            <tr>
                <td>選項：</td>
                <td><input type="text" name="option[]" required maxlength="50" /></td>
            </tr>
        </table>
        <p style="text-align: left;"> <button type="button" name="add_option" id="add_option">新增選項</button> </p>
        <p> <input type="submit" name="submit" value="傳 送" /> </p>
    </form>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
if ($redirect) echo "window.location.replace('issue_list.php');";
?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
  new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
