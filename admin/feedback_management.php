<?
require_once('get_feedback_status.php');


function getResidentName($email,$lan) {
  $sql = $GLOBALS['conn']->prepare('SELECT * FROM resident WHERE email=?');
  $sql->bind_param('s', $email);
  $sql->execute();
  $result = $sql->get_result();
  if ($record = $result -> fetch_assoc()) {
    if($lan=='zh'){
      return $record['chi_last_name'].''.$record['chi_first_name'];
    }else{
      return $record['eng_last_name'].' '.$record['eng_first_name'];
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <?require("common/head.php");?>
  <script>
  $(document).ready(function() {
    $('#action_div').hide();
    $('#image_list').hide();
    $('#selected_department').hide();
    $('#response_detail').hide();
    $('#show_detail').hide();
    $('#resopnder').hide();
    $table=$('#feedback_table').DataTable();
    $table.on('click', '.responded,.request', function () {
      $('.progress').hide();
      $('#resopnder').hide();
      $("#response_list").hide();
      if($(this).attr("class")=='responded'){
        $title = "<?=$feedback_management_total?>";
        $resopnder_id = $(this).attr("id");
        $('#resopnder').show();
      }else{
        $title = "<?=$feedback_management_question?>";
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
        $('#response_list  > tbody:last-child').append('<tr><th><?=$feedback_management_num?></th><th><?=$feedback_management_replier?></th><th><?=$feedback_management_content?></th><th><?=$feedback_management_process?></th><th><?=$feedback_management_date?></th></tr>');
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
      $('#cover').show();
      document.getElementById('blur').style.filter = 'blur(10px)';
    };
    $('#hide').click(function(){
      hideContent();
    });
    $table.on('click', '.referral,.response', function () {
      $('#action_div').show();
      $('#cover').show();
      $action = $(this).attr("class");
      if($action=="referral"){
        $action_title = '<?=$feedback_management_actionTitle?>';
        $("#department_id").prop('required',true);
        $('#selected_department').show();
      }
      else if($action=="response"){
        $action_title = '<?=$feedback_management_respondTitle?>';
        $("#response_textarea").prop('required',true);
        $('#response_detail').show();
      }
      $record_id = $(this).attr("name");
      $("#action_title").text($action_title);
      $(".show_record_id").text($record_id);
      $(".selected_record_id").val($record_id);
      $("#action").val($action);
      document.getElementById('blur').style.filter = 'blur(10px)';
    });
    $('.cancel').click(function(){
      $("#response_textarea").prop('required',false);
      $("#department_id").prop('required',false);
      $('#action_div').hide();
      $('#cover').hide();
      $('#selected_department').hide();
      $('#response_detail').hide();
      document.getElementById('blur').style.filter = 'none';
    });
     var hideContent = function() {
      $('#show_detail').hide();
      $('#cover').hide();
      $('#image_list').hide();
      $('#resopnder').hide();
      document.getElementById('blur').style.filter = 'none';
     };
  });


  </script>

    <style>
    #action_div,#show_detail{
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    /* width: 700px; */
    width: 80%;
    padding: 20px;
    /* background-color: rgb(40, 204, 158); */
    background-color: rgb(204, 239, 255);
    border-radius: 25px;
    font-size:25px;
    z-index: 9999;
    }
    #cover {
    position: fixed;
    top: 0;
    left: 0;
    background: rgba(0,0,0,0.6);
    z-index: 50;
    width: 100%;
    height: 100%;
    display: none;
   }
    #action_div  textarea{
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
    <h1><?=$feedback_management_title?></h1>
  </header>
  <div id="action_div" style="display:none">
    <table border="0" style="width:100%;">
      <form action="update_feedback_process.php" method="post" >
        <h2 id="action_title"></h2>
        <input type="hidden" class="selected_record_id" name="selected_record_id" value=""/>
        <input type="hidden" id="action" name="action" value=""/>
        <input type="hidden"  name="percentage" value="100" id="percentage"/>
        <input type="hidden"  name="page" value="../feedback_management.php"/>
        <tr>
          <td><?=$feedback_management_questionId?></td>
          <td><span class="show_record_id" name="show_record_id"></span></td>
        </tr>
        <tr id="selected_department">
          <td><?=$feedback_management_department ?></td>
          <td>
            <select class="" id="department_id" name="department_id">
            <option value="">請選擇...</option>
            <?
              $sql = $GLOBALS['conn']->prepare('SELECT department_id FROM staff_position WHERE position_id = ?');
              $sql->bind_param('s', $_SESSION['user']['position']);
              $sql->execute();
              $result = $sql->get_result();
              $record = $result -> fetch_assoc();
              $sql = $GLOBALS['conn']->prepare('SELECT * FROM department WHERE department_id !=?');
              $sql->bind_param('s', $record['department_id']);
              $sql->execute();
              $result = $sql->get_result();
              while ($record = $result -> fetch_assoc()) { ?>
                <option value="<?=$record['department_id']?>" name="<?=$record['department_id']?>"><?=$record['department_chi_name']?></option>
              <? } ?>
            </select></td>
          </tr>
          <tr id="response_detail">
            <td><?=$feedback_management_reply?></td>
            <td><textarea id="response_textarea" name="response" rows="8" cols="80" placeholder="請填寫"></textarea></td>
          </tr>
            <p> <input type="submit" name="submit" id="submit" value="<?=$confirm?>"/> </p>
            <p> <input type="button" class="cancel" value="<?=$feedback_management_return?>" /> </p>
        </form>
    </table>
  </div>
  <div id="show_detail" style="display:none">
    <table border="0" style="width:100%;">
      <tr>
        <td><?=$feedback_management_questionId?></td>
        <td><p class="show_record_id"></p></td>
      </tr>
      <tr id="resopnder">
        <td><?=$feedback_management_record?></td>
        <td>
          <table id="response_list" style="width:100%;">
            <tr>
              <th><?=$feedback_management_num?></th>
              <th><?=$feedback_management_replier?></th>
              <th><?=$feedback_management_content?></th>
              <th><?=$feedback_management_process?></th>
              <th><?=$feedback_management_date?></th>
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
        <td><?=$feedback_management_pic?></td>
        <td id="photo_list"></td>
      </tr>
    </table>
    <center><button id="hide"><?=$feedback_management_return?></button></center>
  </div>

  <div id="cover">
</div>

  <div class="row" id="blur">
    <div class="col-lg-12">
      <div class="card">
        <table border="1" id="feedback_table" class="display" style="width:100%">
          <thead>
            <tr>
              <th><?=$feedback_management_tbQuest?></th>
              <th><?=$feedback_management_resChiName?></th>
              <th><?=$feedback_management_resEngName?></th>
              <th><?=$feedback_management_questType?></th>
              <th><?=$feedback_management_questDetail?></th>
              <th><?=$feedback_management_processStage?></th>
              <th><?=$feedback_management_refreshTime?></th>
              <th><?=$feedback_management_action?></th>
            </tr>
          </thead>
          <tbody>
            <?
            $sql = $conn->prepare('SELECT * FROM feedback ORDER BY status');
            $sql->execute();
            $result = $sql->get_result();
            while ($record = $result->fetch_assoc()) { ?>
              <tr>
                <td><?=$record['record_id']?></td>
                <td><?=getResidentName($record['user_email'],'zh')?></td>
                <td><?=getResidentName($record['user_email'],'en')?></td>
                <td><?=getCategoryName($record['category_id'])?></td>
                <td>
                <?php
                    $photo_list =json_decode($record['feedback_photo']);
                    $photo_list =json_encode($photo_list);
                    $photo_list =preg_replace('/\s+/', '_@#', $photo_list);
                    $record_detail = preg_replace('/\s+/', '_@#', $record['record_details']);
                  echo "<button name=".$record['record_id']." value=".$record_detail." class='request' id=".$photo_list.">$feedback_management_resPro</button>";

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
                    if ($confirm) {
                      $respones = preg_replace('/\s+/', '_@#', json_encode($list_response));
                      echo "<button name=".$record['record_id']." id=".$all_percentage." class='responded' value=".$respones.">$feedback_management_processRecord</button>";
                    }
                  ?>
                </td>
                <td><?php
                        if($all_percentage==0 || $all_percentage==100){
                          if($record['status']==1){
                            if($_SESSION['lang'] == 'zh'){
                              echo '已轉介';
                            } else echo 'Referred';
                          }else
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
                    if($record['status']==0){
                      echo "<button name=".$record['record_id']." class='referral'>$feedback_management_refer</button>";
                      echo "<button name=".$record['record_id']." class='response'>$feedback_management_resButton</button>";
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
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
