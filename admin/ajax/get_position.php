<?
require('../../common/conn.php');

session_start();
if (!isSet($_SESSION['user'])) {
    echo "";
} else if ($_SESSION['user']['type'] != 'staff') {
    echo "";
} else {
    $position = array();
    if (isSet($_GET['department_id'])) {
        $sql = $conn->prepare('SELECT * FROM staff_position WHERE department_id=?');
        $sql->bind_param('s', $_GET['department_id']);
        $sql->execute();
        $result = $sql->get_result();
        while ($record = $result -> fetch_assoc()) {
            $position[] = $record;
        }
        echo json_encode($position);
    }
}
?>
