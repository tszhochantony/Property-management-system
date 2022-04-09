<!DOCTYPE html>
<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';
$vote_ok = true;
$redirect = false;

$issue_id = isSet($_POST['issue_id']) ? $_POST['issue_id'] : 0;
$issue_title = '';
$issue_details = '';
$raise_flag = 0;
$raised_by = '';
$cutoff_date = '';

$choice = isSet($_POST['choice']) ? $_POST['choice'] : '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'resident') {
    header('Location: index.php');
} else if ($_SESSION['user']['is_owner'] != '1') {
    header('Location: main.php');
}

if (isSet($_POST['submit'])) {
    $sql = $conn->prepare('SELECT * FROM voting_record WHERE issue_id=? AND owner_email=?');
    $sql->bind_param('is', $issue_id, $_SESSION['user']['account']);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $alert_msg = $issue_vote_already;
        $redirect = true;
        $vote_ok = false;
    }
    if ($vote_ok) {
        $sql = $conn->prepare('INSERT INTO voting_record (issue_id, owner_email, owner_choice) VALUES (?, ?, ?)');
        $sql->bind_param('iss', $issue_id, $_SESSION['user']['account'], $choice);
        $sql->execute();

        if ($sql->affected_rows > 0) {
            $alert_msg = $issue_vote_success;
            $redirect = true;
        } else $alert_msg = $issue_vote_fail;

    }
} elseif (isSet($_GET['issue_id'])) {
    $sql = $conn->prepare('SELECT * FROM voting_record WHERE issue_id=? AND owner_email=?');
    $sql->bind_param('is', $_GET['issue_id'], $_SESSION['user']['account']);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows == 0) {
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
        $alert_msg = $issue_vote_already;
        $redirect = true;
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

?>
<html>
<head>
    <?require("common/head.php");?>
    <style>
    div, table, th, td{
      box-sizing: border-box ;
      color:#000;
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
      border-collapse: collapse;
      /* table-layout: fixed; */
    }
    th,td{
      padding: 10px;
      border: 1px solid #000;

    }
    th:first-child,
    td:first-child{
      left: 0;
      width: 130px;
      background-color: #ddd;
    }
    td{
      color:#FFF;
    }
    form > table >tbody> tr >td, tr > td > table >tbody > tr > td{
      display: revert !important;
    }
    </style>
</head>
<body>
    <?include('common/menuNew.php');?>
    <header>
        <h1><?=$issue_voting_page?></h1>
    </header>
    <form class="formCss" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <input type="hidden" name="issue_id" value="<?=$issue_id?>">
        <table border="0">
            <tr>
                <th><?=$issue_id_details?></th>
                <td><?=$issue_id?></td>
            </tr>
            <tr>
                <th><?=$issue_title_details?></th>
                <td><?=$issue_title?></td>
            </tr>
            <tr>
                <th><?=$issue_content_details?></th>
                <td><?=$issue_details?></td>
            </tr>
            <tr>
                <th><?=$issue_promoter_details?></th>
                <td><?=$record['raise_flag'] == 1 ? $issue_management_office : getOwnerName($raised_by)?></td>
            </tr>
            <tr>
                <th><?=$issue_vote_deadline_details?></th>
                <td><?=$cutoff_date?></td>
            </tr>
            <?if (date('Y-m-d') < $record['cutoff_date']) {?>
                <tr>
                    <th><?=$issue_your_choice?></th>
                    <td>
                        <table border="1">
                            <tr>
                                <th><?=$issue_option?></th>
                                <th style='color:#fff;'><?=$issue_choice?></th>
                            </tr>
                            <?
                            $sql = $conn->prepare('SELECT * FROM issue_choice WHERE issue_id=?');
                            $sql->bind_param('i', $issue_id);

                            $sql->execute();
                            $result = $sql->get_result();

                            while ($record = $result -> fetch_assoc()) { ?>
                                <tr>
                                    <td style="color:#000;"><?=$record['choice_chi_desc']?></td>
                                    <td style="color:#000;"> <input type="radio" name="choice" value="<?=$record['choice_id']?>" required /> </td>
                                </tr>
                                <?}?>
                            </table>
                        </td>
                    </tr>

            <?}?>
            </table>
            <input type="submit" name="submit" value="<?=$edit_resident_send?>"> </td>
        </form>
    </body>
    <script type="text/javascript">
    <?php
    if ($alert_msg <> '') echo "alert('$alert_msg');";
    if ($redirect) echo "window.location.replace('issue_list.php');";
    ?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
