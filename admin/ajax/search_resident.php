<?
require('../../common/conn.php');

session_start();
if (!isSet($_SESSION['user'])) {
    echo "";
} else if ($_SESSION['user']['type'] != 'staff') {
    echo "";
} else {
    $resident = array();
    if (isSet($_GET['mobile_phone'])) {
        $pStmt  = 'SELECT eng_first_name, eng_last_name, chi_first_name, chi_last_name, email, floor, room_no, chi_building_name, eng_building_name FROM resident ';
        $pStmt .= 'INNER JOIN property ON resident.property_id = property.property_id ';
        $pStmt .= 'INNER JOIN building ON property.building_id = building.building_id ';
        $pStmt .= 'WHERE mobile_phone=?';

        $sql = $conn->prepare($pStmt);
        $sql->bind_param('s', $_GET['mobile_phone']);
        $sql->execute();
        $result = $sql->get_result();
        while ($record = $result -> fetch_assoc()) {
            $resident[] = $record;
        }
        echo json_encode($resident);
    }
}
?>
