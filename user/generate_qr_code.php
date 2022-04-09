<?
include('../lib/phpqrcode/qrlib.php');
require('../common/conn.php');
session_start();
require_once('../lang/lang_conn.php');


if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'resident') {
    header('Location: index.php');
} else {
    $sql = $conn->prepare('SELECT * FROM resident_qr_code WHERE email=?');
    $sql->bind_param('s', $_SESSION['user']['account']);

    $sql->execute();
    $result = $sql->get_result();
    if ($record = $result->fetch_assoc()) {
        QRcode::png($record['qr_code']);
    }
}
?>
