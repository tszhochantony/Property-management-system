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

function getPropertyInfo($property_id) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM property WHERE property_id=?');
    $sql->bind_param('i', $property_id);
    $sql->execute();
    $result = $sql->get_result();
    if ($record = $result -> fetch_assoc()) {
        return getBuildingInfo($record['building_id']).' '.$record['floor'].'樓 '.$record['room_no'].'室';
    } else return '<i>查無資料</i>';
}

function getBuildingInfo($building_id) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM building WHERE building_id=?');
    $sql->bind_param('s', $building_id);
    $sql->execute();
    $result = $sql->get_result();
    if ($record = $result -> fetch_assoc()) {
        return $record['chi_building_name'];
    }
}

function getStatus($status_id) {
    switch ($status_id) {
        case 0:
            if($_SESSION['lang'] == 'zh')
                return "停用";
            else return "Disabled";
        case 1:
            if($_SESSION['lang'] == 'zh')
                return "有效";
            else return "Available";
        case 2:
            if($_SESSION['lang'] == 'zh')
                return "臨時";
            else return "Temporary";
    }
}

function reverse($status_id) {
    switch ($status_id) {
        case 0: return 1;
        case 1: return 0;
    }
}

function isOwner($email) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM owner WHERE user_email=?');
    $sql->bind_param('s', $email);

    $sql->execute();
    $result = $sql->get_result();

    return $result->num_rows > 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <?require("common/head.php");?>
    <script>
    $(document).ready(function() {
        $('#resident_table').DataTable();
    });
    </script>
</head>

<body>
    <?include('common/menuNew.php');?>
    <header>
        <h1><?=$resident_management_title?></h1>
    </header>
    <p><a class="abutton" href="create_resident_2.php"><?=$resident_management_create?></a></p>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <table border="1" id="resident_table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th><?=$assign_owner_english?></th>
                            <th><?=$assign_owner_chinese?></th>
                            <th><?=$assign_owner_unit?></th>
                            <th><?=$resident_management_ownerstatus?></th>
                            <th><?=$assign_owner_status?></th>
                            <th><?=$assign_owner_manage?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $sql = $conn->prepare('SELECT * FROM resident WHERE status<>2');
                        $sql->execute();
                        $result = $sql->get_result();
                        while ($record = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?=$record['eng_last_name'].', '.$record['eng_first_name']?></td>
                                <td><?=$record['chi_last_name'] != NULL || $record['chi_first_name'] != NULL ? $record['chi_last_name'].' '.$record['chi_first_name'] : '<i>查無資料</i>'?></td>
                                <td><?=getPropertyInfo($record['property_id'])?></td>
                                <td><?=isOwner($record['email']) ? $resident_yes: $resident_no?></td>
                                <td><?=getStatus($record['status'])?></td>
                                <td>
                                    <a href="edit_resident.php?email=<?=$record['email']?>"><button><?=$resident_management_modify?></button></a>
                                    <?if ($record['status'] != 2) {?>
                                        <a href="change_resident_status.php?email=<?=$record['email']?>&status=<?=reverse($record['status'])?>"><button onclick="return confirm('<?=$resident_confirm?><?=$record['status'] == 1 ? $resident_stop : $resident_active?><?=$resident_use?>（<?=$record['email']?>）?');"><?=$record['status'] == 1 ? $resident_stop : $resident_active?><?=$resident_use?></button></a>
                                        <?}?>
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
