<?
require('conn.php');


if (isSet($_GET['msg'])) {
    $sql = $conn->prepare('SELECT lang, chi_first_name, chi_last_name, eng_first_name, eng_last_name FROM resident_qr_code INNER JOIN resident ON resident.email=resident_qr_code.email WHERE qr_code=?');
    $sql->bind_param('s', $_GET['msg']);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows == 1) {
        if ($record = $result -> fetch_assoc()) {
            echo json_encode(array('type'=>'resident', 'record'=>$record));
        }
    } else {
        $date = date('Y-m-d');
        $sql = $conn->prepare('SELECT eng_first_name, eng_last_name, lang FROM visitor WHERE hash=? AND access_date=?');
        $sql->bind_param('ss', $_GET['msg'], $date);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows == 1) {
            if ($record = $result -> fetch_assoc()) {
                echo json_encode(array('type'=>'visitor', 'record'=>$record));
            }
        } else echo json_encode(array('type'=>'404', 'record'=>'404'));
    }
} else echo json_encode(array('type'=>'404', 'record'=>'404'));

$conn->close();
?>
