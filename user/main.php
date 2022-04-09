<!DOCTYPE html>
<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';

if (!isSet($_SESSION['user'])) {
  header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'resident') {
  header('Location: index.php');
}
?>
<html>
<head>
  <?require("common/head.php");?>
</head>
<body>
  <?include('common/menuNew.php');?>
  <header>
    <h1><?=$main_page_title?></h1>
  </header>
  <center>
      <div id="wrap" style="display: none;justify-content:space-around;background-color:#FFF;box-shadow: 0px 0px 15px 4px #222222;">
        <a href="invite_visitors.php">
          <img alt="" style=" width: 100px; display: block;margin: 5% 0;" src="<?=$pic_invite_Visitor?>">
        </a>
        <?
        if(isSet($_SESSION['user'])){
          if ($_SESSION['user']['is_owner'] == 1)  {
          echo "
        <a href='issue_list.php'>
          <img alt='' style='width: 100px; display: block;margin: 5% 0;' src='$pic_issue_list'>
        </a>";}
      }
        ?>
        <!-- <a class="thumbnail thumb-mar-bottom" target="_blank">
          <img alt="" style=" width: 100%; display: block;" src="../common/img/invite_ch.png">
        </a>
        <a class="thumbnail thumb-mar-bottom" target="_blank">
          <img alt="" style=" width: 100%; display: block;" src="../common/img/invite_ch.png">
        </a> -->
      </div>
    </center>
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
