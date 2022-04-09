<!DOCTYPE html>
<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';

if (!isSet($_SESSION['user'])) {
  header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
  header('Location: index.php');
}
?>
<html>
<head>
  <?require("common/head.php");?>
  <style>@media(orientation:portrait){ #wrap{ display: flex !important;margin-top: 50%; width: 70%;} }</style>
</head>
<body>
  <?include('common/menuNew.php');?>
  <header>
    <h1><?php if($_SESSION['lang'] == 'zh'){
      echo "WeProp 管 理 頁 面";
    } else if($_SESSION['lang'] == 'en'){
      echo "WeProp Management HomePage";
    } ?>
  </h1>
</header>
</div><!-- /container -->
<center>
  <div id="wrap" style="display: none;justify-content:space-around;background-color:#FFF;box-shadow: 0px 0px 15px 4px #222222;">
    <a href="visitor_registration.php">
      <img alt="" style=" width: 100px; display: block;margin: 5% 0;" src="<?=$pic_ID_card_scanner?>">
    </a>

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
