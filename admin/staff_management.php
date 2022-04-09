<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

if (!isSet($_SESSION['user'])) {
  header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
  header('Location: index.php');
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
  }
}

function getDepartmentInfo($department_id) {
  $sql = $GLOBALS['conn']->prepare('SELECT * FROM department WHERE department_id=?');
  $sql->bind_param('s', $department_id);
  $sql->execute();
  $result = $sql->get_result();
  if ($record = $result -> fetch_assoc()) {
    return $record['department_eng_name'].' '.$record['department_chi_name'];
  }
}

function getPositionInfo($position_id) {
  $sql = $GLOBALS['conn']->prepare('SELECT * FROM staff_position WHERE position_id=?');
  $sql->bind_param('s', $position_id);
  $sql->execute();
  $result = $sql->get_result();
  if ($record = $result -> fetch_assoc()) {
    return $record['position_eng_name'].' '.$record['position_chi_name'];
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
  <script>
  $(document).ready(function() {
    $('#staff_table').DataTable();
  });
  </script>
</head>
<body>
  <?include('common/menuNew.php');?>
  <header>
    <h1><?=$staff_management_title?></h1>
  </header>
  <p><a class="abutton" href="create_staff.php"><?=$staff_management_create?></a></p>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <table border="1" id="staff_table" class="display" style="width:100%">
          <thead>
            <tr>
              <th><?=$staff_management_english?></th>
              <th><?=$staff_management_chinese?></th>
              <th><?=$staff_management_department?></th>
              <th><?=$staff_management_position?></th>
              <th><?=$staff_management_status?></th>
              <th><?=$staff_management_manage?></th>
            </tr>
          </thead>
          <tbody>
            <?
            $sql = $conn->prepare('SELECT * FROM staff');
            $sql->execute();
            $result = $sql->get_result();
            while ($record = $result->fetch_assoc()) { ?>
              <tr>
                <td><?=$record['eng_last_name'].', '.$record['eng_first_name']?></td>
                <td><?=$record['chi_last_name'] != NULL || $record['chi_first_name'] != NULL ? $record['chi_last_name'].' '.$record['chi_first_name'] : '<i>查無資料</i>'?></td>
                <td><?=getDepartmentInfo($record['position_id'])?></td>
                <td><?=getPositionInfo($record['position_id'])?></td>
                <td><?=getStatus($record['status'])?></td>
                <td>
                  <a href="edit_staff.php?id=<?=$record['staff_id']?>"><button><?=$staff_management_modify?></button></a>
                  <?if ($record['staff_id'] != $_SESSION['user']['account']) {?>
                    <a href="change_staff_status.php?id=<?=$record['staff_id']?>&status=<?=reverse($record['status'])?>"><button onclick="return confirm('<?=$resident_confirm?><?=$record['status'] == 1 ? $resident_stop : $resident_active?><?=$resident_use?>（<?=$record['staff_id']?>）?');"><?=$record['status'] == 1 ? $resident_stop : $resident_active?><?=$staff_use?></button></a>
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
  <script src="../common/js/classie.js"></script>
  <script src="../common/js/gnmenu.js"></script>
  <script>
  new gnMenu( document.getElementById( 'gn-menu' ) );
  </script>
  </html>
  <?$conn->close();?>
