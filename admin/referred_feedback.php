<?
require_once('get_feedback_status.php');
?>
<!DOCTYPE html>
<html>
<head>
  <?require("common/head.php");?>
  <script src="../common/js/classie.js"></script>
  <script src="../common/js/gnmenu.js"></script>
  <script>
    new gnMenu( document.getElementById( 'gn-menu' ) );
  </script>
  <script>
  $(document).ready(function() {
    $('#resopnder').hide();
    $('.progress').hide();
    $('#image_list').hide();
    $('#response_div').hide();
    $('#show_detail').hide();
    $("#response_list").hide();
    $table=$('#feedback_table').DataTable();
    $table.on('click', '.response', function () {
      $per =100;
      $record_id = $(this).attr("name");
      $percent = $(this).attr("id");
      $per -= $percent;
      $start =0;
      $("#percentage option").remove();
      while ($start<=$per) {
        $("#percentage").append($("<option></option>").attr("value", $start).text($start + "%"));
        $start +=10;
      }
      $(".show_record_id").text($record_id);
      $(".pre_percent").text($percent);
      $(".hold_percent").text($percent);
      $(".selected_record_id").val($record_id);
      $('#response_div').show();
      document.getElementById('blur').style.filter = 'blur(10px)';
    });
    $('.cancel').click(function(){
      $('.row').show();
      $('#response_div').hide();
      document.getElementById('blur').style.filter = 'none';
    });
    $('#percentage').change(function(){
        $total_per = parseInt($(".hold_percent").text());
        $total_per += parseInt($(this).val());
        $(".pre_percent").text($total_per);
    });
    $table.on('click', '.responded,.request', function () {
      $('.progress').hide();
      $('#resopnder').hide();
      $("#response_list").hide();
      if($(this).attr("class")=='responded'){
        $title = "<?=$feedback_progress?>";
        $resopnder_id = $(this).attr("id");
        $('#resopnder').show();
      }else{
        $title = "<?=$feedback_list_question?>";
        $resopnder_id = '';
        if($(this).attr("id").length>10){
          $('#image_list').show();
          $image_array = JSON.parse($(this).attr("id").replace(/_@#/g, ' '));
          $('#photo_list').empty();
          for($i=0;$i<$image_array.length;$i++){
            $('#photo_list').append('<img src="../user/images/'+$image_array[$i]+'" width="300" height="300">');
          }
        }
      }
      showContent($(this).attr("name"),$title,$(this).attr("value").replace(/_@#/g, ' '),$resopnder_id,$(this).attr("class"));
    });
    $('#hide').click(function(){
      hideContent();
    });
    var showContent = function($record_id,$title,$content,$resopnder_id,$action_type) {
      $(".show_record_id").text($record_id);
      $("#resopnder_id").text($resopnder_id);
      $("#title").text($title);
      $("#detail_content").text($content);
      if($action_type=='responded'){
        $("#detail_content").text("");
        $("#detail_content1").hide();
        $('.progress').show();
        $("#response_list").show();
        $(".progress-bar").css("width",$resopnder_id+"%");
        $(".progress-bar").text($resopnder_id+"%");
        $uncode = jQuery.parseJSON($content);
        $index = 1;
        $("#response_list tr").remove();
        $('#response_list  > tbody:last-child').append('<tr><th><?=$feedback_list_id?></th><th><?=$feedback_list_responer?></th><th><?=$feedback_list_content?></th><th><?=$feedback_list_progress?></th><th><?=$feedback_list_date?></th></tr>');
        while($index<=Object.keys($uncode['name']).length){
        $('#response_list  > tbody:last-child').append('<tr><td>'+$uncode['name'][$index]+'</td><td>'
                                                        +$uncode['staff'][$index]+'</td><td>'
                                                        +$uncode['response'][$index]+'</td><td>'
                                                        +$uncode['percentage'][$index]+'%</td><td>'
                                                        +$uncode['time'][$index]+'</td></tr>');
        $index +=1;
        }
      } else{$("#detail_content1").show();}
      $('#show_detail').show();
      document.getElementById('blur').style.filter = 'blur(10px)';
    };
     var hideContent = function() {
      $('#show_detail').hide();
      $('#resopnder').hide();
      $('#image_list').hide();
      document.getElementById('blur').style.filter = 'none';
     };
  });
  </script>
      <style>
    #response_div,#show_detail{
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

    #response_div textarea{
      width: 100%;
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
      width: 130px;
      background-color: #ddd;
    }
    #response_list > tbody > tr > td {
      font-size: 16px;
    }
    #response_div {
      font-size: 16px;
    }
    center > button {
      margin: 5% 0;
    }
</style>
</head>

<body>
<?include('common/menuNew.php');?>
 <header>
    <h1><?=$your_jobs?></h1>
  </header>
  <div id="response_div" style="display:none">
  <form action="update_feedback_process.php" method="post">
        <center><h2><?=$please_record_your_progress?></h2></center>
        <table border="0" style="width:100%">
          <input type="hidden" class="selected_record_id" name="selected_record_id" value=""/>
          <input type="hidden"  name="action" value="response"/>
          <input type="hidden"  name="page" value="../referred_feedback.php"/>
          <p class="hold_percent" name="hold_percent" style="display:none" ></p>
            <tr>
              <td><?=$your_jobs_id?></td>
              <td><p class="show_record_id" name="show_record_id"></p></td>
            </tr>
            <tr>
              <td><?=$your_job_progress?></td>
              <td>
                <p> <select name="percentage" id="percentage">
                </select></p>
              </td>
            </tr>
            <tr>
              <td><?=$your_job_total_progress?></td>
              <td><p class="pre_percent" name="pre_percent"></p></td>
            </tr>
            <tr>
              <td><?=$your_job_content?> </td>
              <td><textarea name="response" rows="8" cols="80" placeholder="請填寫" required ></textarea></td>
            </tr>
          </table>
            <p> <input type="submit" name="submit" value="<?=$confirm?>" style="margin: 0 0;"/> </p>
            <p> <input type="button" class="cancel" value="<?=$feedback_list_return?>" style="margin: 0 0;"/> </p>
        </form>
  </div>
  <div id="show_detail" style="display:none;">
    <table border="0" style="width:100%;">
      <tr>
        <td><?=$feedback_list_questionId?></td>
        <td><p class="show_record_id"></p></td>
      </tr>
      <tr id="resopnder">
        <td><?=$feedback_list_rec?></td>
        <td>
          <table id="response_list" style="width:100%;">
            <tr>
              <th>編號</th>
              <th>回應者</th>
              <th>內容</th>
              <th>進度</th>
              <th>日期</th>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
       <td id="title"></td>
        <td class="progress">
          <div class="progress">
            <div class="progress-bar"></div>
          </div>
        </td>
      <td id="detail_content1"><p id="detail_content"></p></td>
      </tr>
      <tr id="image_list">
        <td>圖片</td>
        <td id="photo_list"></td>
      </tr>
    </table>
    <center><button id="hide">返回</button></center>
  </div>
  <div class="row" id="blur">
    <div class="col-lg-12">
      <div class="card">
        <table border="1" id="feedback_table" class="display" style="width:100%">
          <thead>
            <tr>
              <th><?=$your_jobs_id?></th>
              <th><?=$your_jobs_category?></th>
              <th><?=$your_jobs_view?></th>
              <th><?=$your_jobs_status?></th>
              <th><?=$your_jobs_update_time?></th>
              <th><?=$your_jobs_action?></th>
            </tr>
          </thead>
          <tbody>
            <?
            $sql = $GLOBALS['conn']->prepare('SELECT position_id FROM staff WHERE staff_id=?');
            $sql->bind_param('s', $_SESSION['user']['account']);
            $sql->execute();
            $result = $sql->get_result();
            $record = $result->fetch_assoc();

            $sql = $GLOBALS['conn']->prepare('SELECT department_id FROM staff_position WHERE position_id=?');
            $sql->bind_param('s', $record['position_id']);
            $sql->execute();
            $result = $sql->get_result();
            $record = $result->fetch_assoc();

            $sql = $GLOBALS['conn']->prepare('SELECT * FROM feedback_referral WHERE department_id=?');
            $sql->bind_param('s', $record['department_id']);
            $sql->execute();
            $result = $sql->get_result();
            while ($record = $result->fetch_assoc()) {
              $sql = $conn->prepare('SELECT * FROM feedback WHERE record_id =?');
              $sql->bind_param('i', $record['record_id']);
              $sql->execute();
              $result2 = $sql->get_result();
              $record2 = $result2->fetch_assoc();?>
                <tr>
                  <td><?=$record2['record_id']?></td>
                  <td><?=getCategoryName($record2['category_id'])?></td>
                  <td>
                  <?php
                  $photo_list = json_decode($record2['feedback_photo']);
                  $photo_list = json_encode($photo_list);
                  $photo_list =preg_replace('/\s+/', '_@#', $photo_list);
                  $record_detail = preg_replace('/\s+/', '_@#', $record2['record_details']);
                  echo "<button name=".$record2['record_id']." id=".$photo_list." class='request' value=".$record_detail.">$question_view</button>";
                  $all_percentage = 0;
                  $list_response = array();
                  $counter = 1;
                    $sql = $conn->prepare('SELECT * FROM feedback_response WHERE record_id=? ORDER BY timestamp');
                    $sql->bind_param('s',$record['record_id']);
                    $sql->execute();
                    $result2 = $sql->get_result();
                    $confirm = false;
                    $lateupdatetime=$record2['timestamp'];
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
                    if ($confirm) {
                      $respones = preg_replace('/\s+/', '_@#', json_encode($list_response));
                      echo "<button name=".$record['record_id']." id=".$all_percentage." class='responded' value=".$respones.">$progress_record</button>";
                    }
                  ?>
                </td>
                  <td>
                    <?php
                      if($all_percentage==0 || $all_percentage==100){
                        echo getStatus($record2['status']);
                      }
                      else{
                        echo getStatus(1.5);
                      }
                    ?>
                  </td>
                  <td><?=$lateupdatetime?></td>
                  <td>
                    <?php
                      if($all_percentage<100){
                        echo "<button name=".$record2['record_id']." id=".$all_percentage." class='response'>$update_progress</button>";
                      }
                    ?>
                </td>
                </tr>
            <?}?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
<?$conn->close();?>
