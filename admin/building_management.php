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

function isEmpty($building_id) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM property WHERE building_id=?');
    $sql->bind_param('s', $building_id);

    $sql->execute();
    $result = $sql->get_result();

    return $result->num_rows == 0;
}
?>
<!DOCTYPE html>
<html>
<head>
  <?require("common/head.php");?>
  <script>
  $(document).ready(function() {
    $('#building_table').DataTable();
  });
  </script>
</head>

<body>
  <?include('common/menuNew.php');?>
  <header>
    <h1><?php echo $building_management_title ?></h1>
  </header>
  <p><a class="abutton" href="create_building.php"><?php echo $building_management_add?></a></p>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <table border="1" id="building_table" class="display" style="width:100%;">
          <thead>
            <tr>
              <th><?php echo $building_management_id?></th>
              <th><?php echo $building_management_zhName?></th>
              <th><?php echo $building_management_enName?></th>
              <th><?php echo $building_management_manage?></th>
            </tr>
          </thead>
          <tbody>
            <?
            $sql = $conn->prepare('SELECT * FROM building');
            $sql->execute();
            $result = $sql->get_result();
            while ($record = $result -> fetch_assoc()) { ?>
              <tr>
                <td><?=$record['building_id']?></td>
                <td><?=$record['chi_building_name']?></td>
                <td><?=$record['eng_building_name']?></td>
                <td>
                  <a href="edit_building.php?building_id=<?=$record['building_id']?>"><button><?php echo $building_management_modify?></button></a>
                  <?if (isEmpty($record['building_id'])) {?><a href="delete_building.php?building_id=<?=$record['building_id']?>"><button onclick="return confirm('<?php echo $building_management_confirmDelete ?>（<?=$record['building_id'].' - '.$record['eng_building_name'].' '.$record['chi_building_name']?>）?');"><?php echo $building_management_delete ?></button></a><?}?>
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
