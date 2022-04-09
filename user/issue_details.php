<!DOCTYPE html>
<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$label_id = array();
$label = array();
$data = array();



if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'resident') {
    header('Location: index.php');
} else if ($_SESSION['user']['is_owner'] != '1') {
    header('Location: main.php');
}

if (isSet($_GET['issue_id'])) {
    $result_text = '';
    $sql = $conn->prepare('SELECT * FROM issue_choice WHERE issue_id=?');
    $sql->bind_param('i', $_GET['issue_id']);
    $sql->execute();
    $result = $sql->get_result();
    while ($record = $result -> fetch_assoc()) {
        array_push($label_id, $record['choice_id']);
        array_push($label, $record['choice_chi_desc']);
    }

    foreach ($label_id as $choice_id) {
        $sql = $conn->prepare('SELECT owner_choice, choice_chi_desc, count(*) AS count FROM voting_record LEFT JOIN issue_choice ON voting_record.owner_choice=issue_choice.choice_id AND voting_record.issue_id=issue_choice.issue_id WHERE voting_record.issue_id=? AND owner_choice=?');
        $sql->bind_param('is', $_GET['issue_id'], $choice_id);
        $sql->execute();
        $result = $sql->get_result();
        while ($record = $result -> fetch_assoc()) {
            array_push($data, $record['count']);
        }
    }

    $sql = $conn->prepare('SELECT * FROM issue WHERE issue_id=?');
    $sql->bind_param('i', $_GET['issue_id']);
    $sql->execute();
    $result = $sql->get_result();
    if ($record = $result->fetch_assoc()) {
        $issue_id = $record['issue_id'];
        $issue_title = $record['issue_title'];
        $issue_details = $record['issue_details'];
        $raise_flag = $record['raise_flag'];
        $raised_by = $record['raised_by'];
        $cutoff_date = $record['cutoff_date'];
    }
} else {
    $alert_msg = $database_error;
    $redirect = true;
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

function getVotingResult($issue_id) {
    $result_text = '';
    $sql = $GLOBALS['conn']->prepare('SELECT owner_choice, choice_chi_desc, count(*) AS count FROM voting_record INNER JOIN issue_choice ON voting_record.owner_choice=issue_choice.choice_id AND voting_record.issue_id=issue_choice.issue_id WHERE voting_record.issue_id=? GROUP BY owner_choice ORDER BY count DESC');
    $sql->bind_param('i', $issue_id);
    $sql->execute();
    $result = $sql->get_result();
    while ($record = $result -> fetch_assoc()) {
        if ($_SESSION['lang'] == 'zh') {
            return '選項 '.$record['choice_chi_desc'].' 獲得最多的票數，共獲得'.$record['count'].'票。';
        } else {
            return 'Option '.$record['choice_chi_desc'].' received '.$record['count'].' vote and won the voting.';
        }
    }
}
?>
<html>
<head>
    <?require("common/head.php");?>
    <meta name="viewport" content="initial-scale=0.8, maximum-scale=0.8, user-scalable=yes" />
    <style>
      div, table, th, td{
        box-sizing: border-box ;
      }
      .outerDiv{
        position: relative;
        width: 100%;
        padding-left: 120px;
        overflow: hidden;
      }
      .innerDiv{
        overflow: auto;
      }
      table{
        table-layout: fixed;
        border-collapse: collapse;
      }
      th,td{
        padding: 10px;
        border: 1px solid #000;
        white-space: nowrap;
      }
      th:first-child,
      td:first-child{
        left: 0;
        width: 120px;
        background-color: #ddd;
      }
      td{
        color: #FFF;
      }
    </style>
</head>
<body>
    <?include('common/menuNew.php');?>
    <header>
        <h1><?=$issue_datails?></h1>
    </header>
    <center>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <input type="hidden" name="issue_id" value="<?=$issue_id?>">
        <table border="0">
            <tr>
                <th><?=$issue_id_details?></th>
                <td style="display: table-cell;"><?=$issue_id?></td>
            </tr>
            <tr>
                <th><?=$issue_title_details?></th>
                <td style="display: table-cell;"><?=$issue_title?></td>
            </tr>
            <tr>
                <th><?=$issue_content_details?></th>
                <td style="display: table-cell;"><?=$issue_details?></td>
            </tr>
            <tr>
                <th><?=$issue_promoter_details?></th>
                <td style="display: table-cell;"><?=$record['raise_flag'] == 1 ? $issue_management_office : getOwnerName($raised_by)?></td>
            </tr>
            <tr>
                <th><?=$issue_vote_deadline_details?></th>
                <td style="display: table-cell;"><?=$cutoff_date?></td>
            </tr>
            <tr>
                <th><?=$issue_status_details?></th>
                <td style="display: table-cell;"><?=date('Y-m-d') > $cutoff_date ? $issue_vote_ended : checkVotingStatus($issue_id)?></td>
            </tr>
            <?if (date('Y-m-d') > $cutoff_date) {?>
                <tr>
                    <th><?=$issue_vote_result_details?></th>
                    <td>
                        <?=getVotingResult($issue_id)?>
                        <div style="width: 100%; height: 100%;padding: 0;display: block;margin: auto">
                            <canvas id="stat" width="100" height="100" >Canvas does not support on this browser!</canvas>
                        </div>
                        <script>
                        var ctx = document.getElementById('stat');
                        var stat = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: [<?foreach ($label as $index=>$value) {?><?=$index == 0 ? '': ', '?>'<?=$value?>'<? } ?>],
                                datasets: [{
                                    label: '<?=$issue_voting_result?>',
                                    data: [<?foreach ($data as $index=>$value) {?><?=$index == 0 ? '': ', '?>'<?=$value?>'<? } ?>],
                                    backgroundColor: [
                                        <?foreach ($label as $index=>$value) {?>
                                            <?=$index == 0 ? '': ', '?>'rgba(<?=rand(0, 255)?>, <?=rand(0, 255)?>, <?=rand(0, 255)?>, 0.5)'
                                        <? } ?>
                                    ],
                                    borderWidth: 1
                                }]
                            }
                        });
                    </script>

                </td>
            </tr>
            <?}?>
        </table>
        <p style="text-align: center;">
            <?if (checkVotingStatus($_GET['issue_id']) == $issue_unvoted && date('Y-m-d') <= $cutoff_date) { ?><a href="vote_issue.php?issue_id=<?=$_GET['issue_id']?>"><button type="button" name="button"><?=$issue_vote_for_this?></button></a> <?}?>
            <a href="issue_list.php"><button type="button" name="button"><?=$issue_back?></button></a>
        </p>
    </form>
  </center>
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
