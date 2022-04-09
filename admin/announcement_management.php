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

function getStaffName($staff_id) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM staff WHERE staff_id=?');
    $sql->bind_param('s', $staff_id);

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
    $('#announcement_table').DataTable();
  });
  </script>
</head>

<body>
  <?include('common/menuNew.php');?>
  <header>
    <h1><?php echo $admin_menuNew_announcement ?></h1>
  </header>
  <p><a class="abutton" href="broadcast_message.php"><?php echo $admin_menuNew_email?></a></p>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <table border="1" id="announcement_table" class="display" style="width:100%;">
          <thead>
            <tr>
              <th><?=$announcement_sent_time_2?></th>
              <th><?=$announcement_sender_2?></th>
              <th><?=$announcement_email_topic_2?></th>
              <th><?=$announcement_expire_date_2?></th>
              <th><?php echo $building_management_manage?></th>
            </tr>
          </thead>
          <tbody>
            <?
            $sql = $conn->prepare('SELECT * FROM announcement_record ORDER BY timestamp DESC');
            $sql->execute();
            $result = $sql->get_result();
            while ($record = $result -> fetch_assoc()) { ?>
              <tr>
                <td><?=$record['timestamp']?></td>
                <td><?=getStaffName($record['staff_id']).' ('.$record['staff_id'].')'?></td>
                <td><?=$record['announcement_title']?></td>
                <td><?=$record['expire_date']?></td>
                <td> <a href="announcement_details.php?record_id=<?=$record['record_id']?>" target="popup" onclick="window.open('announcement_details.php?record_id=<?=$record['record_id']?>', 'popup', 'width=840,height=600'); return false;"> <button type="button" name="button"><?=$view_details?></button> </a> </td>
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
