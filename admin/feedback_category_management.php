<?
require('../common/conn.php');
session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

function getStatus($status_id) {
    switch ($status_id) {
        case 0: 
            if($_SESSION['lang'] == 'zh'){
                return '已停用';
            } else return 'Disabled';
        case 1: 
            if($_SESSION['lang'] == 'zh'){
                return '已啟用';
            } else return 'Enabled';
    }
}

function reverse($status_id) {
    switch ($status_id) {
        case 0: return 1;
        case 1: return 0;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <?require("common/head.php");?>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#feedback_category_table').DataTable();
    });
    </script>
</head>

<body>
    <?include('common/menuNew.php');?>
    <header>
        <h1><?=$feedback_category_management_title?></h1>
    </header>
    <p><a class="abutton" href="create_feedback_category.php"><?=$feedback_category_management_add?></a></p>
    <div class="row" id="blur">
        <div class="col-lg-12">
            <div class="card">
                <table border="1" id="feedback_category_table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th><?=$feedback_category_management_id?></th>
                            <th><?=$feedback_category_management_chiName?></th>
                            <th><?=$feedback_category_management_engName?></th>
                            <th><?=$feedback_category_management_status?></th>
                            <th><?=$feedback_category_management_manage?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $sql = $conn->prepare('SELECT * FROM feedback_category');
                        $sql->execute();
                        $result = $sql->get_result();
                        while ($record = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?=$record['category_id']?></td>
                                <td><?=$record['category_chi_name']?></td>
                                <td><?=$record['category_eng_name']?></td>
                                <td><?=getStatus($record['status'])?></td>
                                <td>
                                    <a href="edit_feedback_category.php?category_id=<?=$record['category_id']?>"><button><?=$feedback_category_management_modify?></button></a>
                                    <a href="change_feedback_category_status.php?category_id=<?=$record['category_id']?>&status=<?=reverse($record['status'])?>"><button onclick="return confirm('確定<?=$record['status'] == 1 ? '停' : '啟'?>用此類別（<?=$record['category_id']?>）?');"><?=$record['status'] == 1 ? $resident_stop : $resident_active?><?=$feedback_category_management_use?></button></a>
                                </td>
                            </tr>
                        <? } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
