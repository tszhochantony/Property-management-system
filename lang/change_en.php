<?php
require('../common/conn.php');
session_start();
$_SESSION['lang'] = 'en';
if ($_SESSION['user']['type'] == 'resident') {
    $sql = $conn->prepare('UPDATE resident SET lang="en" WHERE email=?');
    $sql->bind_param('s', $_SESSION['user']['account']);
    $sql->execute();
}
header('Refresh: 0; url ='. $_SERVER['HTTP_REFERER'] );
exit;
//header('Location: ../admin/main.php');

?>
