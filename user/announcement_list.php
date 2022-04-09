<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$duplicate_check = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'resident') {
    header('Location: index.php');
} else {
  $email = $_SESSION['user']['account'];
}

$today = date('Y-m-d');
if ($_SESSION['user']['is_owner'] == 1) {
    $sql = $conn->prepare('SELECT * FROM announcement_record INNER JOIN announcement_recipient ON announcement_record.record_id=announcement_recipient.record_id WHERE recipient="owner" OR recipient=? AND expire_date>=?');
    $sql->bind_param('ss', getResidentBuilding($_SESSION['user']['account']), $today);
} else {
    $sql = $conn->prepare('SELECT * FROM announcement_record INNER JOIN announcement_recipient ON announcement_record.record_id=announcement_recipient.record_id WHERE recipient=? AND expire_date>=?');
    $sql->bind_param('ss', getResidentBuilding($_SESSION['user']['account']), $today);
}
$sql->execute();
$result = $sql->get_result();

function getResidentBuilding($email) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM resident INNER JOIN property ON property.property_id=resident.property_id WHERE email=?');
    $sql->bind_param('s', $email);
    $sql->execute();
    $result = $sql->get_result();
    if ($record = $result->fetch_assoc()) {
        return $record['building_id'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<?require("common/head.php");?>
    <script>
    $(document).ready(function() {
      $('#annonuncement_table').DataTable();
    });
    </script>
  <style>
    #annonuncement_table_wrapper{
      margin-left: 3%;
    }
  </style>
</head>
<body>
    <?include('common/menuNew.php');?>
    <header>
        <h1><?php echo $user_menuNew_announcement_list ?></h1>
    </header>
<div class="row" style="color: black;">
  <div class="col-lg-12">
    <div class="card" style="display: table;width: 90%;">
      <table border="1" id="annonuncement_table" class="display" style="width:100%;">
        <thead>
          <tr>
            <th><?=$announcement_sent_time_2?></th>
            <th><?=$announcement_email_topic_2?></th>
            <th><?php echo $building_management_manage?></th>
          </tr>
        </thead>
        <tbody>
            <? while ($record = $result -> fetch_assoc()) { ?>
                <? if ($record['record_id'] == $duplicate_check) continue; ?>
                <tr>
                    <td><?=$record['timestamp']?></td>
                    <td><?=$record['announcement_title']?></td>
                    <td> <a href="announcement_details.php?record_id=<?=$record['record_id']?>" target="popup" onclick="window.open('announcement_details.php?record_id=<?=$record['record_id']?>', 'popup', 'width=840,height=600'); return false;"> <button type="button" name="button"><?=$view_details?></button> </a> </td>
                </tr>
                <? $duplicate_check = $record['record_id']; ?>
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
