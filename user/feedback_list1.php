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
  $email = $_SESSION['user']['account'];
}

function getCategoryName($category_id) {
  if($_SESSION['lang'] == 'zh'){
  $sql = $GLOBALS['conn']->prepare('SELECT category_chi_name FROM feedback_category WHERE category_id=?');
  $sql->bind_param('s', $category_id);
  $sql->execute();
  $result = $sql->get_result();
  if ($record = $result -> fetch_assoc()) {
    return $record['category_chi_name'];
  }
}else {
  $sql = $GLOBALS['conn']->prepare('SELECT category_eng_name FROM feedback_category WHERE category_id=?');
  $sql->bind_param('s', $category_id);
  $sql->execute();
  $result = $sql->get_result();
  if ($record = $result -> fetch_assoc()) {
    return $record['category_eng_name'];
  }
}
}

function getStatus($status_id) {
  switch ($status_id) {
case 0:
  if($_SESSION['lang'] == 'zh')
    return "未處理";
  else return "Not Processed";
case 1:
    if($_SESSION['lang'] == 'zh')
      return "已轉介";
    else return "Referred to another department";
case 1.5:
    if($_SESSION['lang'] == 'zh')
      return "處理中";
    else return "Processing";
case 2:
  if($_SESSION['lang'] == 'zh')
    return "已處理";
  else return "Processed";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <?require("common/head.php");?>
  <script>
  $(document).ready(function() {
    $('#show_detail').hide();
    $('#resopnder').hide();
    $('#image_list').hide();
    $table=$('#feedback_table').DataTable();
    $table.on('click', '.request,.responded', function () {
      $('.progress').hide();
      $('#resopnder').hide();
      $("#response_list").hide();
      if($(this).attr("class")=='responded'){
        $title = "<?=$feedback_progress?>";
        $resopnder_id = $(this).attr("id");
        $('#resopnder').show();
      }else{
        $title =  "<?=$feedback_problem?>";
        $resopnder_id = '';
        if($(this).attr("id").length>0){
          $('#image_list').show();
          $image_array = JSON.parse($(this).attr("id").replace(/_@#/g, ' '));
          $('#photo_list').empty();
          for($i=0;$i<$image_array.length;$i++){
            $('#photo_list').append('<img src="images/'+$image_array[$i]+'" width="300" height="300">');
          }
        }
      }
      showContent($(this).attr("name"),$title,$(this).attr("value").replace(/_@#/g, ' '),$resopnder_id,$(this).attr("class"));
    });
    var showContent = function($record_id,$title,$content,$resopnder_id,$action_type) {
      $(".show_record_id").text($record_id);
      $("#resopnder_id").text($resopnder_id);
      $("#title").text($title);
      $("#detail_content").text($content);
      if($action_type=='responded'){
        $("#detail_content").text("");
        $("#detail_content").hide();
        $('.progress').show();
        $("#response_list").show();
        $(".progress-bar").css("width",$resopnder_id+"%");
        $(".progress-bar").text($resopnder_id+"%");

        $uncode = jQuery.parseJSON($content);
        $index = 1;
        $("#response_list tr").remove();
        $("#response_list > center").remove();
        $('#response_list  > tbody:last-child').append('<tr id="response_listPC"><th><?=$feedback_id?></th><th><?=$feedback_replier?></th><th><?=$feedback_content?></th><th><?=$feedback_progress?></th><th><?=$feedback_date?></th></tr>');
        while($index<=Object.keys($uncode['name']).length){
        $('#response_list  > tbody:last-child').append('<tr id="response_listPC"><td>'+$uncode['name'][$index]+'</td><td>'
                                                        +$uncode['staff'][$index]+'</td><td>'
                                                        +$uncode['response'][$index]+'</td><td>'
                                                        +$uncode['percentage'][$index]+'%</td><td>'
                                                        +$uncode['time'][$index]+'</td></tr>');
        $index +=1;
        }
        $('#response_list  > tbody:last-child').append('<center><a href="feedback_details.php?rId=' + $record_id + '"' + 'target="popup" class="response_listMobile" style="display:none;"> <button type="button" name="button"><?=$view_details?></button> </a></center>');
      } else{$("#detail_content").show();}
      $('#show_detail').show();
      document.getElementById('blur').style.filter = 'blur(10px)';
    };
    $('#hide').click(function(){
      hideContent();
    });
     var hideContent = function() {
      $('#show_detail').hide();
      $('#resopnder').hide();
      $('#image_list').hide();
      document.getElementById('blur').style.filter = 'none';
     };
  });
  </script>
  <style>
    #show_detail{
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    padding: 20px;
    background-color: rgb(40, 204, 158);
    font-size:25px;
    z-index: 9999;
    }
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
      box-sizing: border-box ;
      color:#000;
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
      border-collapse: collapse;
      /* table-layout: fixed; */
    }
    th,td{
      padding: 10px;
      border: 1px solid #000;

    }
    th:first-child,
    td:first-child{
      left: 0;
      width: 120px;
      background-color: #ddd;
    }
    a > button {
      margin: 5% 0;
    }
    .card {
      display: table !important;
      width: 90% !important;
    }
    #feedback_table_wrapper{
      margin-left: 3%;
    }
    #detail_content, .show_record_id{
      text-align: center !important;
    }
</style>
</head>

<body>
<?include('common/menuNew.php');?>
 <header>
    <h1><?=$feedback_list_yourQu?></h1>
  </header>
  <p><a class="abutton" href="report_feedback.php"><?=$user_menuNew_feedback?></a></p>
  <div id="show_detail" style="display:none">
    <table border="0" style="width:100%;">
      <tr>
        <td><?=$feedback_list_questionId?> </td>
        <td><p class="show_record_id"></p></td>
      </tr>
      <tr id="resopnder">
        <td><?=$feedback_list_rec ?> </td>
        <td>
          <table id="response_list" style="width: 100%; font-size:18px;">
            <tr>
              <th><?=$feedback_list_id ?></th>
              <th><?=$feedback_list_responer?></th>
              <th><?=$feedback_list_content?></th>
              <th><?=$feedback_list_progress?></th>
              <th><?=$feedback_list_date?></th>
            </tr>
          </table>
        </td>

      </tr>
      <tr>
       <td id="title"></td>
        <td class="progress">
          <div class="progress" style="width:100%">
            <div class="progress-bar"></div>
          </div>
        </td>
      <td id="detail_content"><p id="detail_content"></p></td>
      </tr>
      <tr id="image_list">
        <td>圖片</td>
        <td id="photo_list"></td>
      </tr>
    </table>
    <center><a href="#"><button id="hide"><?=$feedback_list_return?></button><a></center>
  </div>
  <div class="row" id="blur">
    <div class="col-lg-12">
      <div class="card">
        <table border="1" id="feedback_table" class="display" style="width:100%">
          <thead>
            <tr>
              <th><?=$feedback_list_qustID?></th>
              <th><?=$feedback_list_questionType?></th>
              <th><?=$feedback_list_process?></th>
              <th><?=$feedback_list_time?></th>
              <th><?=$feedback_list_look?></th>
            </tr>
          </thead>
          <tbody>
            <?
            $sql = $conn->prepare('SELECT * FROM feedback WHERE user_email=? ORDER BY status');
            $sql->bind_param('s',$email);
            $sql->execute();
            $result = $sql->get_result();
            while ($record = $result->fetch_assoc()) { ?>
            <?php
                  $all_percentage = 0;
                  $list_response = array();
                  $counter = 1;
                    $sql = $conn->prepare('SELECT * FROM feedback_response WHERE record_id=? ORDER BY timestamp');
                    $sql->bind_param('s',$record['record_id']);
                    $sql->execute();
                    $result2 = $sql->get_result();
                    $confirm = false;
                    $lateupdatetime=$record['timestamp'];
                    while ($record22 = $result2->fetch_assoc()) {
                      $lateupdatetime=$record22['timestamp'];
                      if(!$confirm){
                        $confirm = true;
                      }
                      $list_response['name'][$counter] = $counter;
                      $list_response['staff'][$counter] = $record22['staff_id'];
                      $list_response['response'][$counter] = $record22['response'];
                      $list_response['percentage'][$counter] = $record22['percentage'];
                      $list_response['time'][$counter] = date("m-d-Y;h:i:sa", strtotime($record22['timestamp']));;
                      $all_percentage += $record22['percentage'];
                      $counter+=1;
                    }
                  ?>
              <tr>
                <td><?=$record['record_id']?></td>
                <td><?=getCategoryName($record['category_id'])?></td>
                <td><?php
                        if($all_percentage==0 || $all_percentage==100){
                            echo getStatus($record['status']);
                        }
                        else{
                          echo getStatus(1.5);
                        }
                        ?>
                  </td>
                  <td><?=$lateupdatetime?></td>
                <td>
                  <?php
                  $photo_list = preg_replace('/\s+/', '_@#', $record["feedback_photo"]);
                  $record_detail = preg_replace('/\s+/', '_@#', $record['record_details']);
                  echo "<button name=".$record['record_id']." value=".$record_detail." class='request' id=".$photo_list.">$feedback_list_your</button>";


                  if ($confirm) {
                      $respones = preg_replace('/\s+/', '_@#', json_encode($list_response));
                      echo "<button name=".$record['record_id']." id=".$all_percentage." class='responded' value=".$respones.">$feedback_list_progressRec</button>";
                    }
                  ?>
                </td>
              </tr>
            <? } ?>

          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
