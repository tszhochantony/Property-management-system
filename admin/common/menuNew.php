<?
require('../common/conn.php');
require_once('../lang/lang_conn.php');

//session_start();
if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
}

// if(!isset($_SESSION['lang'])){
// 	$_SESSION['lang'] == 'zh';
// 	require_once('../lang/chinese.php');
// }else if($_SESSION['lang'] == 'zh'){
// 	require_once('../lang/chinese.php');
// }else if($_SESSION['lang'] == 'en'){
// 	require_once('../lang/english.php');
// }
?>
<script>
  //Preloader
  $(document).ready(function() {
		// Animate loader off screen
      setTimeout(function(){
        $('.loader_bg').fadeToggle();
      },1500);
	});
</script>
<div class="loader_bg"><div class="loader"></div></div>
<div class="container">
	<ul id="gn-menu" class="gn-menu-main">
		<li class="gn-trigger" style="">
			<a class="gn-icon gn-icon-menu"><span>Menu</span></a>
			<nav class="gn-menu-wrapper ">
				<div class="gn-scroller">
					<ul class="gn-menu">
						<li>
							<a class="" href="logout.php"><i class="fas fa-sign-out-alt"></i><?php echo $admin_menuNew_logout ?></a>
							<a class="" href="../lang/change_zh.php">轉換中文介面</a>
							<a class="" href="../lang/change_en.php">Change English Language</a>

						</li>
						<!-- <li>
						<a class="" href="#"><i class="fas fa-home"></i>空白</a>
					</li> -->
				</ul>
			</div><!-- /gn-scroller -->
		</nav>
	</li>
	<div class="headScroll">
		<ul class="leftRight" >
			<li>
				<a href="index.php"><?php echo $admin_menuNew_homepage?></a>
			</li>
			<?
			if(isSet($_SESSION['user'])){
				if ($_SESSION['user']['position'] == 'admin') {
				echo "
				<li>
				<a class='gn-icon-menu1' href=''>$admin_menuNew_housing</a>
				<nav class='gn-menu-wrapper1'>
					<div class='gn-scroller'>
						<ul class='gn-menu'>
							<li>
								<a class='' href='building_management.php'><i class='fas fa-home'></i>$admin_menuNew_building</a>
							</li>
							<li>
								<a class='' href='property_management.php'><i class='fas fa-home'></i>$admin_menuNew_unit</a>
							</li>
						</ul>
					</div><!-- /gn-scroller -->
				</nav>
			</li>
			<li><a href='resident_management.php'>$admin_menuNew_household</a></li>
			<li>
				<a class='gn-icon-menu3' href=''>$staff_management_title</a>
				<nav class='gn-menu-wrapper3'>
					<div class='gn-scroller'>
						<ul class='gn-menu'>
							<li>
								<a class='' href='staff_management.php'><i class='fas fa-user-tie'></i>&nbsp;&nbsp;$admin_menuNew_staff</a>
							</li>
							<li>
								<a class='' href='staff_position_management.php'><i class='fas fa-user-tag'></i>$admin_menuNew_position</a>
							</li>
							<li>
								<a class='' href='department_management.php'><i class='fas fa-building'></i>&nbsp;&nbsp;$admin_menuNew_department</a>
							</li>
						</ul>
					</div><!-- /gn-scroller -->
				</nav>
			</li>
			<li>
				<a class='gn-icon-menu2' href=''>$admin_menuNew_announcement</a>
				<nav class='gn-menu-wrapper2'>
					<div class='gn-scroller'>
						<ul class='gn-menu'>
                            <li>
                                <a class='' href='announcement_management.php'><i class='fas fa-reply-all'></i>$admin_menuNew_email_list</a>
                            </li>
							<li>
								<a class='' href='broadcast_message.php'><i class='fas fa-reply-all'></i>$admin_menuNew_email</a>
							</li>
						</ul>
					</div><!-- /gn-scroller -->
				</nav>
			</li>
      <li>
        <a class='gn-icon-menu4' href='' >$admin_menuNew_problemBig</a>
        <nav class='gn-menu-wrapper4'>
          <div class='gn-scroller'>
            <ul class='gn-menu'>
            <li>
              <a class='' href='feedback_management.php'><i class='fas fa-list-ol'></i>$admin_menuNew_problem</a>
            </li>
              <li>
                <a class='' href='feedback_category_management.php'><i class='fas fa-arrows-alt-v'></i>&nbsp;&nbsp;&nbsp;$admin_menuNew_category</a>
              </li>
            </ul>
          </div><!-- /gn-scroller -->
        </nav>
      </li>
	  <li><a href='visitor_registration.php' target='_blank'>$visitor_registration_title</a></li>
    <li><a href='hold_for_collection.php'>$hold_for_collection_title</a></li>
	<li><a href='analysis_chart.php' target='_blank'>$analysis_report</a></li>
  <li><a href='visitor_list.php'>$visitor_list_title</a></li>";
			} else {
				echo "<li>
				<a class='gn-icon-menu1' href='' style='display:none'>$admin_menuNew_housing</a>
				<nav class='gn-menu-wrapper1'>
					<div class='gn-scroller'>
						<ul class='gn-menu'>
							<li>
								<a class='' href='building_management.php'><i class='fas fa-home'></i>$admin_menuNew_building</a>
							</li>
							<li>
								<a class='' href='property_management.php'><i class='fas fa-home'></i>$admin_menuNew_unit</a>
							</li>
						</ul>
					</div><!-- /gn-scroller -->
				</nav>
			</li>
			<li>
				<a class='gn-icon-menu3' href='' style='display:none'>$admin_menuNew_staff</a>
				<nav class='gn-menu-wrapper3'>
					<div class='gn-scroller'>
						<ul class='gn-menu'>
							<li>
								<a class='' href='staff_management.php'><i class='fas fa-user-tie'></i>&nbsp;&nbsp;$admin_menuNew_staff</a>
							</li>
							<li>
								<a class='' href='staff_position_management.php'><i class='fas fa-user-tag'></i>$admin_menuNew_position</a>
							</li>
							<li>
								<a class='' href='department_management.php'><i class='fas fa-building'></i>&nbsp;&nbsp;$admin_menuNew_department</a>
							</li>
						</ul>
					</div><!-- /gn-scroller -->
				</nav>
			</li>
			<li>
				<a class='gn-icon-menu2' href='' style='display:none'>$admin_menuNew_announcement</a>
				<nav class='gn-menu-wrapper2'>
					<div class='gn-scroller'>
						<ul class='gn-menu'>
                            <li>
                                <a class='' href='announcement_management.php'><i class='fas fa-reply-all'></i>$admin_menuNew_email_list</a>
                            </li>
							<li>
								<a class='' href='broadcast_message.php'><i class='fas fa-reply-all'></i>$admin_menuNew_email</a>
							</li>
						</ul>
					</div><!-- /gn-scroller -->
				</nav>
			</li>
        <li><a href='referred_feedback.php'>$show_jobs</a></li>";
			}
		}
		?>
			<!--<li><a href="edit_personal_information.php">更 改 個 人 資 料</a></li>-->

			<li>
				<a class="gn-icon-menu5" href="" style="display:none">XXXXX</a>
				<nav class="gn-menu-wrapper5 ">
					<div class="gn-scroller">
						<ul class="gn-menu">
							<li>
								<a class="" href="#"><i class="fas fa-user"></i>XX</a>
							</li>
						</ul>
					</div><!-- /gn-scroller -->
				</nav>
			</li>
		</ul>
	</div>
	<!-- <li><a class="codrops-icon codrops-icon-drop" href="http://tympanus.net/codrops/?p=16030"><span>Back to the Codrops Article</span></a></li>-->
</ul>
