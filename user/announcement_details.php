<?
require('../common/conn.php');
session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';

function getResidentBuilding($email) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM resident INNER JOIN property ON property.property_id=resident.property_id WHERE email=?');
    $sql->bind_param('s', $email);
    $sql->execute();
    $result = $sql->get_result();
    if ($record = $result->fetch_assoc()) {
        return $record['building_id'];
    }
}

function getRecipients($record_id) {
    $text = '';
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM announcement_recipient WHERE record_id=?');
    $sql->bind_param('i', $record_id);

    $sql->execute();
    $result = $sql->get_result();
    while ($record = $result -> fetch_assoc()) {
        if ($record['recipient'] == 'owner') $text .= '業主、';
        else $text .= $record['recipient'].'住戶、';
    }
    return substr($text, 0, -3);
}

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'resident') {
    header('Location: index.php');
} else if (isSet($_GET['record_id'])) {
    if ($_SESSION['user']['is_owner'] == 1) {
        $sql = $conn->prepare('SELECT * FROM announcement_record INNER JOIN announcement_recipient ON announcement_record.record_id=announcement_recipient.record_id WHERE (recipient="owner" OR recipient=?) AND announcement_record.record_id=?');
        $sql->bind_param('si', getResidentBuilding($_SESSION['user']['account']), $_GET['record_id']);
    } else {
        $sql = $conn->prepare('SELECT * FROM announcement_record INNER JOIN announcement_recipient ON announcement_record.record_id=announcement_recipient.record_id WHERE recipient=? AND announcement_record.record_id=?');
        $sql->bind_param('si', getResidentBuilding($_SESSION['user']['account']), $_GET['record_id']);
    }

    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows == 0) {
        header('Location: index.php');
    } else if ($record = $result -> fetch_assoc()) {
        if ($record['expire_date'] < date('Y-m-d')) {
            header('Location: index.php');
        } else {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <?require("common/head.php");?>
            <style>
            div, table, th, td{
                box-sizing: border-box;
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
            #message_content,#message_content > p, #message_content > p > span{
              color:unset !important;
              background:#fff !important;
            }
            </style>
        </head>
        <body>
            <center>
                <header><h2 style="color:#FFF"><?php echo $announcement_detail ?></h2></header>
                <div class="table">
                    <table border="0">
                        <tr>
                          <th><?=$announcement_email_receiver_2?></th>
                          <td><?=getRecipients($record['record_id'])?></td>
                        </tr>
                        <tr>
                            <th><?=$announcement_expire_date?></th>
                            <td><?=$record['expire_date']?></td>
                        </tr>
                        <tr>
                            <th><?=$announcement_sent_time?></th>
                            <td><?=$record['timestamp']?></td>
                        </tr>
                        <tr>
                            <th><?=$announcement_email_topic?></th>
                            <td><?=$record['announcement_title']?></td>
                        </tr>
                        <tr>
                            <th><?=$announcement_email_content?></th>
                            <td id="message_content"><?=$record['announcement_content']?></td>
                        </tr>
                    </table>
                </div>
            </center>
        </body>
        </html>

        <?
        }
    }
} else echo "error";
$conn->close();?>
