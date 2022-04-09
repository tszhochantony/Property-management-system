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

function getBuildingInfo($building_id) {
  $sql = $GLOBALS['conn']->prepare('SELECT * FROM building WHERE building_id=?');
  $sql->bind_param('s', $building_id);
  $sql->execute();
  $result = $sql->get_result();
  if ($record = $result -> fetch_assoc()) {
    return $record['eng_building_name'].' '.$record['chi_building_name'];
  }
}

function isEmpty($property_id) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM resident WHERE property_id=?');
    $sql->bind_param('i', $property_id);

    $sql->execute();
    $result = $sql->get_result();

    return $result->num_rows == 0;
}

function getOwner($property_id) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM owner WHERE property_id=?');
    $sql->bind_param('i', $property_id);

    $sql->execute();
    $result = $sql->get_result();

    if ($record = $result -> fetch_assoc()) {
        if (is_null($record['user_email'])) return '<i>查無資料</i>';
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
    $('#property_table').DataTable();
  });
  </script>
</head>

<body>
  <?include('common/menuNew.php');?>
  <header>
    <h1><?=$property_management_title?></h1>
  </header>
  <p><a class="abutton" href="create_property.php"><?=$property_management_add?></a></p>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <table border="1" id="property_table" class="display" style="width:100%">
          <thead>
            <tr>
              <th><?=$property_management_flat?></th>
              <th><?=$property_management_floor?></th>
              <th><?=$property_management_number?></th>
              <th><?=$property_management_owner?></th>
              <th><?=$property_management_manage?></th>
            </tr>
          </thead>
          <tbody>
            <?
            $sql = $conn->prepare('SELECT * FROM property');
            $sql->execute();
            $result = $sql->get_result();
            while ($record = $result -> fetch_assoc()) { ?>
              <tr>
                <td><?=getBuildingInfo($record['building_id'])?></td>
                <td><?=$record['floor']?></td>
                <td><?=$record['room_no']?></td>
                <td><?=getOwner($record['property_id'])?></td>
                <td>
                    <a href="edit_property.php?property_id=<?=$record['property_id']?>"><button><?=$property_management_modify?></button></a>
                    <a href="assign_owner_1.php?property_id=<?=$record['property_id']?>"><button><?=$property_management_assign?></button></a>
                    <?if (isEmpty($record['property_id'])) {?><a href="delete_property.php?property_id=<?=$record['property_id']?>"><button onclick="return confirm('<?=$property_mangement_confirmDelete?>（<?=getBuildingInfo($record['building_id']).' '.$record['floor'].'樓 '.$record['room_no'].'室'?>）?');"><?=$property_mangement_delete?></button><?}?>
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
