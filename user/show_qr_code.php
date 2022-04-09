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
    <h1><?=$show_qr_code_display?></h1>
    <header>
        <div class="row" id="blur">
          <div class="col-lg-12">
            <div class="card">
                <img src="generate_qr_code.php" class="imgCenter" />
                <br /><a href="update_qr_code.php"><button type="button" name="button"><?=$show_qr_code_refresh?></button></a>
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
