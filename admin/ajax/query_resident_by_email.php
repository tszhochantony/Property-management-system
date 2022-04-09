<?
require('../../common/conn.php');

session_start();
if (!isSet($_SESSION['user'])) {
    echo "";
} else if ($_SESSION['user']['type'] != 'staff') {
    echo "";
} else {
    if (isSet($_GET['email'])) {
        $pStmt  = 'SELECT lang, eng_first_name, eng_last_name, chi_first_name, chi_last_name, floor, room_no, chi_building_name, eng_building_name FROM resident ';
        $pStmt .= 'INNER JOIN property ON resident.property_id = property.property_id ';
        $pStmt .= 'INNER JOIN building ON property.building_id = building.building_id ';
        $pStmt .= 'WHERE email=?';

        $sql = $conn->prepare($pStmt);
        $sql->bind_param('s', $_GET['email']);
        $sql->execute();
        $result = $sql->get_result();
        if ($record = $result -> fetch_assoc()) {
            echo json_encode($record);
        }
    }
}
?>
