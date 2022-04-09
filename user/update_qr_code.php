<?
require('../common/conn.php');
session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'resident') {
    header('Location: index.php');
} else {
    $sql = $conn->prepare('SELECT * FROM resident_qr_code WHERE email=?');
	$sql->bind_param('s', $_SESSION['user']['account']);

	$sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows == 0) {
        $sql = $conn->prepare('INSERT INTO resident_qr_code (email, qr_code) VALUES (?, ?)');
        $sql->bind_param('ss', $_SESSION['user']['account'], hash('sha512', $_SESSION['user']['account'].time()));
        $sql->execute();
    } else {
        $sql = $conn->prepare('UPDATE resident_qr_code SET qr_code=? WHERE email=?');
        $sql->bind_param('ss', hash('sha512', $_SESSION['user']['account'].time()), $_SESSION['user']['account']);
        $sql->execute();
    }
    header('Location: show_qr_code.php');
}
?>
