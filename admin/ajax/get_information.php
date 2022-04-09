<?
require('../../common/conn.php');
 session_start();
 if (!isSet($_SESSION['user'])) {
     echo "";
 } else if ($_SESSION['user']['type'] != 'staff') {
     echo "";
 } else {
    $number = array();
    if (isSet($_GET['theme'])) {
        $start  = $_GET['start_day']."/01";
        $end    = $_GET['end_day'];
        $end_p = explode("/",$_GET['end_day']);
        $end_month = (int)$end_p[1];
        if($end_month==1||$end_month==3||$end_month==5||$end_month==7||$end_month==1||$end_month==10||$end_month==12){
            $end = $end."/31";
        }else if($end_month==4||$end_month==6||$end_month==9||$end_month==11){
            $end = $end."/30";
        }else{
            $end = $end."/29";
        }
        if($_GET['theme']=='visitor'){
            $pStmt =   'SELECT COUNT(*) as numbers,DATE_FORMAT(`access_date`, "%Y/%m") as date
                        FROM `visitor` 
                        WHERE `access_date` >= CAST(? AS DATE)
						AND `access_date` <= CAST(? AS DATE)
                        GROUP BY MONTH(`access_date`)
                        ORDER BY DATE_FORMAT(`access_date`, "%Y/%m")';
        }else if($_GET['theme']=='opinion'){
            $pStmt =   'SELECT COUNT(*) as numbers,DATE_FORMAT(`timestamp`, "%Y/%m") as date
                        FROM feedback 
                        WHERE `timestamp` >= CAST(? AS DATE) 
                        AND `timestamp` <= CAST(? AS DATE)
                        GROUP BY MONTH(`timestamp`)
                        ORDER BY DATE_FORMAT(`timestamp`, "%Y/%m")';
        }
        $sql = $conn->prepare($pStmt);
        $sql->bind_param('ss', $start,$end);
        $sql->execute();
        $result = $sql->get_result();
        while ($record = $result -> fetch_assoc()) {
           array_push($number,$record);
        }
        echo json_encode($number);
    }
}
?>
