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

function getStatus($status) {
    switch ($status) {
        case 0: return '暫存中';
        case 1: return '已取走';
    }
}

function getEngName($email) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM resident WHERE email=?');
    $sql->bind_param('s', $email);
    $sql->execute();
    $result = $sql->get_result();
    if ($record = $result -> fetch_assoc()) {
        return $record['eng_last_name'].', '.$record['eng_first_name'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <?require("common/head.php");?>
    <script>
    $(document).ready(function() {
        $('#hfc_table').DataTable();
    });
</script>
</head>
<body>
    <?include('common/menuNew.php');?>
    <header><h1><?=$hold_for_collection_title?></h1></header>
    <p><a class="abutton" href="create_hfc.php"><?=$hold_for_collection_add?></a></p>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <table border="1" id="hfc_table" class="display" style="width:100%;">
                    <thead>
                        <tr>
                            <th><?=$internal_ref_no?></th>
                            <th><?=$resident_eng_name?></th>
                            <th><?=$tracking_number?></th>
                            <th><?=$issue_status?></th>
                            <th><?=$issue_management?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $sql = $conn->prepare('SELECT * FROM parcel');
                        $sql->execute();
                        $result = $sql->get_result();
                        while ($record = $result -> fetch_assoc()) { ?>
                            <tr>
                                <td><?=$record['ref_num']?></td>
                                <td><?=getEngName($record['resident_email'])?></td>
                                <td><?=$record['tracking_num'] == NULL ? $alert_line4 : $record['tracking_num'] ?></td>
                                <td><?=getStatus($record['status'])?></td>
                                <td>
                                    <?if ($record['status'] == 0) {?>
                                        <a href="return_parcel.php?id=<?=$record['ref_num']?>">
                                            <button onclick="return confirm('請確認以下資訊\n\n住戶英文姓名：<?=getEngName($record['resident_email'])?>\n內部參考編號：<?=$record['ref_num']?>\n包裹追蹤號碼：<?=$record['tracking_num'] == NULL ? "查無資料" : $record['tracking_num'] ?>\n\n確定提取此包裹？');"><?=$hold_for_collection_collect?></button>
                                        </a>
                                    <? } ?>
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
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
