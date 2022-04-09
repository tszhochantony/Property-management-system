<?
require('../common/conn.php');
session_start();
require_once('../lang/lang_conn.php');


if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

$property_id = '';
$building_id = '';
$floor = '';
$room_no = '';
$owner = '';

$user_email = '';

$alert_msg = '';
$redirect = false;

if (isSet($_GET['property_id'])) {
    $property_id = $_GET['property_id'];

    $sql = $conn->prepare('SELECT * FROM property INNER JOIN owner ON property.property_id=owner.property_id WHERE property.property_id=?');
    $sql->bind_param('i', $_GET['property_id']);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if ($record = $result->fetch_assoc()) {
            $building_id = $record['building_id'];
            $floor = $record['floor'];
            $room_no = $record['room_no'];
            $owner = $record['user_email'];
        }

        $sql = $conn->prepare('SELECT * FROM owner WHERE property_id=?');
        $sql->bind_param('i', $record['property_id']);
        $sql->execute();
        $result = $sql->get_result();

        if ($record = $result->fetch_assoc()) {
            $user_email = $record['user_email'];
        }

    } else {
        if($_SESSION['lang'] == 'zh')
            $alert_msg = "錯誤：此單位資料已存在！";
        else $alert_msg = "Error : This unit's information already exist";
        $redirect = true;
    }
} else {
    if($_SESSION['lang'] == 'zh')
            $alert_msg = "查詢資料庫時發生錯誤，請重試！";
    else $alert_msg = "An error has occured when searching the database, please try again !";
    $redirect = true;
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
function getOwner($property_id) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM owner WHERE property_id=?');
    $sql->bind_param('i', $property_id);

    $sql->execute();
    $result = $sql->get_result();

    if ($record = $result -> fetch_assoc()) {
        if (is_null($record['user_email'])) return '<i>無</i>';
        $email = $record['user_email'];
        $sql = $GLOBALS['conn']->prepare('SELECT * FROM resident WHERE email=?');
        $sql->bind_param('s', $email);

        $sql->execute();
        $result = $sql->get_result();

        if ($record = $result -> fetch_assoc()) {
            return $record['eng_last_name'].', '.$record['eng_first_name'].' '.$record['chi_last_name'].$record['chi_first_name'].' ('.$email.')';
        }
    }
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
        <header><h1><?=$assign_owner_title?></h1></header>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <p><?=$assign_owner_current ?><?=getOwner($property_id)?>&nbsp;<?=getOwner($property_id) != '<i>無</i>' ? "<a href='assign_owner_2.php?property_id=$property_id&mode=clear'><button onclick=\"return confirm('$assign_owner_confirmClear".getPropertyInfo($property_id)."的業主資料?');\">$assign_owner_clear</button></a>" : ""?></p>
                    <table border="1" id="resident_table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th><?=$assign_owner_english?></th>
                                <th><?=$assign_owner_chinese?></th>
                                <th><?=$assign_owner_unit?></th>
                                <th><?=$assign_owner_email?></th>
                                <th><?=$assign_owner_status?></th>
                                <th><?=$assign_owner_manage?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            if ($owner == '') {
                                $sql = $conn->prepare('SELECT * FROM resident WHERE status<>2');
                            } else {
                                $sql = $conn->prepare('SELECT * FROM resident WHERE status<>2 AND email<>?');
                                $sql->bind_param('s', $owner);
                            }
                            $sql->execute();
                            $result = $sql->get_result();
                            while ($record = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?=$record['eng_last_name'].', '.$record['eng_first_name']?></td>
                                    <td><?=$record['chi_last_name'] != NULL || $record['chi_first_name'] != NULL ? $record['chi_last_name'].' '.$record['chi_first_name'] : '<i>查無資料</i>'?></td>
                                    <td><?=getPropertyInfo($record['property_id'])?></td>
                                    <td><?=$record['email']?></td>
                                    <td><?=getStatus($record['status'])?></td>
                                    <td>
                                        <a href="assign_owner_2.php?property_id=<?=$property_id?>&email=<?=$record['email']?>"><button onclick="return confirm('<?=$assign_owner_confirm?><?=$record['eng_last_name'].', '.$record['eng_first_name'].' '.$record['chi_last_name'].$record['chi_first_name']?>(<?=$record['email']?>)<?=$assign_owner_as?><?=getPropertyInfo($property_id)?><?=$assign_owner_result?>?');"><?=$assign_owner_assign?></button></a>
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
    if ($redirect) echo "window.location.replace('property_management.php');";
    ?>
    </script>
	<script src="../common/js/classie.js"></script>
    <script src="../common/js/gnmenu.js"></script>
    <script>
      new gnMenu( document.getElementById( 'gn-menu' ) );
    </script>
</html>
<?$conn->close();?>
