<!DOCTYPE html>
<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'resident') {
    header('Location: index.php');
} else if ($_SESSION['user']['is_owner'] != '1') {
    header('Location: main.php');
}

function getOwnerName($email) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM resident WHERE email=?');
    $sql->bind_param('s', $email);

    $sql->execute();
    $result = $sql->get_result();

    if ($record = $result -> fetch_assoc()) {
        return $record['eng_last_name'].', '.$record['eng_first_name'].' '.$record['chi_last_name'].$record['chi_first_name'].' ('.$email.')';
    }
}

function checkVotingStatus($issue_id) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM voting_record WHERE issue_id=? AND owner_email=?');
    $sql->bind_param('is', $issue_id, $_SESSION['user']['account']);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows == 0) return $GLOBALS['issue_unvoted'];
    else return $GLOBALS['issue_voted'];
}


?>
<html>
<head>
	<?require("common/head.php");?>
    <script>
    $(document).ready(function() {
      $('#issue_table').DataTable();
    });
    </script>
 <style>
 #issue_table_wrapper{
   margin-left: 3%;
 }
 </style>
</head>
<body>
    <?include('common/menuNew.php');?>
    <header>
    <h1><?=$issue_list?></h1>
    </header>
        <p><a class="abutton" href="create_issue.php"><?=$create_new_issue?></a></p>
    <div class="row" style="color: black;">
      <div class="col-lg-12">
        <div class="card" style="display: table;width: 90%;">
          <table border="1" id="issue_table" class="display" style="width:100%;">
            <thead>
              <tr>
                <th><?=$issue_id?></th>
                <th><?=$issue_title?></th>
                <th><?=$issue_promoter?></th>
                <th><?=$issue_status?></th>
                <th><?=$issue_management?></th>
              </tr>
            </thead>
            <tbody>
              <?
              $sql = $conn->prepare('SELECT * FROM issue');
              $sql->execute();
              $result = $sql->get_result();
              while ($record = $result -> fetch_assoc()) { ?>
                <tr>
                  <td><?=$record['issue_id']?></td>
                  <td><?=$record['issue_title']?></td>
                  <td><?=$record['raise_flag'] == 1 ? $issue_management_office : getOwnerName($record['raised_by'])?></td>
                  <td><?=date('Y-m-d') > $record['cutoff_date'] ? $issue_vote_ended : checkVotingStatus($record['issue_id'])?></td>
                  <td>
                    <a href="issue_details.php?issue_id=<?=$record['issue_id']?>"><button><?=$issue_view_details?></button></a>
                    <?if (date('Y-m-d') < $record['cutoff_date'] && checkVotingStatus($record['issue_id']) == $issue_unvoted) {?><a href="vote_issue.php?issue_id=<?=$record['issue_id']?>"><button><?=$issue_vote?></button></a><?}?>
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
