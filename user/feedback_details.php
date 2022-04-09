<?
require('../common/conn.php');
session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';


if(isSet($_GET['rId'])) {
    $record_id = $_GET['rId'];
}
else{
    header('Location: feedback_list.php');
}
?>
<!DOCTYPE html>
  <html>
  <head>
      <?require("common/head.php");?>
      <style>
      .progress {
        margin: 0 auto;
        width: 500px;
        background:grey;

      }
      .progress-bar {
        text-align: right;
        color: white;
        width: 0%;
        background-color: green;
        height: 30px;
        border-radius: 4px;
        -webkit-transition: 0.7s linear;
        -webkit-transition-property: width, background-color;
      }
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
      </style>
    </head>
    <body>
      <center>
        <header><h2 style="color:#FFF"><?php echo $feedback_details_title ?></h2></header>
        <?php
        $all_percentage = 0;
        $list_response = array();
        $counter = 1;
        $sql = $conn->prepare('SELECT * FROM feedback_response WHERE record_id=? ORDER BY timestamp');
        $sql->bind_param('s',$record_id);
        $sql->execute();
        $result = $sql->get_result();
        while ($record = $result->fetch_assoc()) {
          $list_response['name'][$counter] = $counter;
          $list_response['staff'][$counter] = $record['staff_id'];
          $list_response['response'][$counter] = $record['response'];
          $list_response['percentage'][$counter] = $record['percentage'];
          $list_response['time'][$counter] = date("m-d-Y;h:i:sa", strtotime($record['timestamp']));;
          $all_percentage += $record['percentage'];
          $counter+=1;
        }
        for($i=1;$i<$counter;$i++){
          echo "<div class='table' style='margin-bottom: 5%;'>
                  <table border='0'>
                  <tr>
                    <th>".$feedback_id."</th>
                    <td>".$list_response['name'][$i]."</td>
                  </tr>
                  <tr>
                    <th>".$feedback_replier."</th>
                    <td>".$list_response['staff'][$i]."</td>
                  </tr>
                  <tr>
                    <th>".$feedback_content."</th>
                    <td>".$list_response['response'][$i]."</td>
                  </tr>
                  <tr>
                    <th>".$feedback_progress."</th>
                    <td class='progress'>
                      <div class='progress' style='width:100%'>
                        <div class='progress-bar' style='font-size:25px; width:".$list_response['percentage'][$i]."%;'>".$list_response['percentage'][$i]."%</div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>".$feedback_date."</th>
                    <td>".$list_response['time'][$i]."</td>
                  </tr>
                  </table>
                </div>
                ";
        }
        echo "<div style='font-size:20px;color:#FFF;margin-bottom:5%;'>
              <table border='0' style='width:70%;'>
              <thead>
                <tr>
                  ".$feedback_progress."
                  </thead>
                  <td class='progress'>
                    <div class='progress' style='width:70%'>
                      <div class='progress-bar' style='font-size:25px; width:".$all_percentage."%;'>".$all_percentage."%</div>
                    </div>
                  </td>
                </tr>
                </table>
              </div>";
        ?>
  </center>
    </body>
    </html>
