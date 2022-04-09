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

function getInviter($email) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM resident WHERE email=?');
    $sql->bind_param('s', $email);

    $sql->execute();
    $result = $sql->get_result();
    if ($record = $result->fetch_assoc()) {
        return $record['eng_last_name'].', '.$record['eng_first_name'];
    }
}

function getStatus($status) {
    switch ($status) {
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
?>
<!DOCTYPE html>
<html>
<head>
  <?require("common/head.php");?>
  <script>
  $(document).ready(function() {
    $('#visitor_table').DataTable();
  });
  </script>
</head>

<body>
  <?include('common/menuNew.php');?>
  <header>
    <h1><?php echo $visitor_list_title ?></h1>
  </header>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <table border="1" id="visitor_table" class="display" style="width:100%;">
          <thead>
            <tr>
              <th><?php echo $visitor_access_date?></th>
              <th><?php echo $visitor_id_no?></th>
              <th><?php echo $visitor_full_name?></th>
              <th><?php echo $visitor_inviter?></th>
              <th><?php echo $visitor_status?></th>
            </tr>
          </thead>
          <tbody>
            <?
            $sql = $conn->prepare('SELECT * FROM visitor ORDER BY access_date DESC');
            $sql->execute();
            $result = $sql->get_result();
            while ($record = $result -> fetch_assoc()) { ?>
              <tr>
                <td><?=$record['access_date']?></td>
                <td><?=$record['id_no']?></td>
                <td><?=$record['eng_last_name'].', '.$record['eng_first_name']?></td>
                <td><?=$record['resident_email'] == null ? '管理處' : getInviter($record['resident_email'])?></td>
                <td><?=$record['resident_email'] == null ? '不適用' : getStatus($record['status'])?></td>
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
