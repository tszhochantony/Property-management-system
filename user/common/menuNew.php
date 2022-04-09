<?
require('../common/conn.php');
require_once('../lang/lang_conn.php');

session_start();
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
  <div class="topleft" style="display: flex;position: absolute;top: 8px;right: 16px;font-size: 18px;color: #FFF;"><a href="./main.php"><?=$home_page?></a></div>
	<div class="topright" style="display: flex;position: absolute;top: 8px;right: 16px;font-size: 18px;color: #FFF;">
    <!-- <a href="hold_for_collection.php" style="color:#000;background: #fff;color: #000;display: block;margin: 0 10px;font-size: 18px;width: 34px;height: 34px;line-height: 35px;text-align: center;border-radius: 50%;transition: 0.3s;transition-property: background, color;">
      <i class="fas fa-archive" style="line-height: 2;"></i>
    </a> -->
    <?=$change_language?>
  </div>

		<div class="container">
			<ul id="gn-menu" class="gn-menu-main" style="z-index:999;">
				<li class="gn-trigger" style="">
					<a class="gn-icon gn-icon-menu"><span>Menu</span></a>
					<nav class="gn-menu-wrapper ">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<li>
									<a class="" href="logout.php"><i class="fas fa-sign-out-alt"></i><?= $admin_menuNew_logout ?></a>
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
				<li class="mobileDisplay">
				    <a href="index.php"><i class="fas fa-sign-out-alt icon-main-nav"></i><?= $admin_menuNew_homepage?></a>
			    </li>
					<?
					if(isSet($_SESSION['user'])){
						if ($_SESSION['user']['is_owner'] != 1)  {
						echo "
				<li><a href='announcement_list.php'><i class='fas fa-exclamation icon-main-nav'style='font-size:30px;padding-top: 20%;'></i>$user_menuNew_announcement_list</a></li>
				<li><a href='feedback_list.php'><i class='fas fa-list icon-main-nav'style='font-size:30px;padding-top: 20%;'></i>$user_menuNew_list</a></li>
				<li><a href='edit_personal_information.php'><i class='fas fa-user-edit icon-main-nav' style='font-size:30px;padding-top: 20%;'></i>$user_menuNew_person</a></li>
				<li><a href='invite_visitors.php'><i class='fas fa-user-plus icon-main-nav'style='font-size:30px;padding-top: 20%;'></i>$invite_visitors</a></li>";
        // <li><a href='hold_for_collection.php'><i class='fas fa-archive icon-main-nav'style='font-size:30px;padding-top: 20%;'></i>$hold_for_collection_title</a></li>
			} else {
					echo "
					<li><a href='announcement_list.php'><i class='fas fa-exclamation icon-main-nav'style='font-size:30px;padding-top: 20%;'></i>$user_menuNew_announcement_list</a></li>
					<li><a href='feedback_list.php'><i class='fas fa-list icon-main-nav'style='font-size:30px;padding-top: 20%;'></i>$user_menuNew_list</a></li>
					<li><a href='edit_personal_information.php'><i class='fas fa-user-edit icon-main-nav'style='font-size:30px;padding-top: 20%;'></i>$user_menuNew_person</a></li>
					<li><a href='invite_visitors.php'><i class='fas fa-user-plus icon-main-nav'style='font-size:30px;padding-top: 20%;'></i>$invite_visitors</a></li>
					<li><a href='issue_list.php'><i class='fas fa-sign-out-alt icon-main-nav'></i>$issue_list</a></li>";
          // <li><a href='hold_for_collection.php'><i class='fas fa-archive icon-main-nav'style='font-size:30px;padding-top: 20%;'></i>$hold_for_collection_title</a></li>
				}
			}
			?>
				<li>
					<a class="gn-icon-menu1" href="" style="display:none">XXXXX</a>
					<nav class="gn-menu-wrapper1 ">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<li>
									<a class="" href="#"><i class="fas fa-user" style="display:none"></i>XX</a>
								</li>
							</ul>
						</div><!-- /gn-scroller -->
					</nav>
				</li>
				<li>
					<a class="gn-icon-menu2" href="" style="display:none">XXXXX</a>
					<nav class="gn-menu-wrapper2 ">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<li>
									<a class="" href="#"><i class="fas fa-user"></i>XX</a>
								</li>
							</ul>
						</div><!-- /gn-scroller -->
					</nav>
				</li>
				<li>
					<a class="gn-icon-menu3" href="" style="display:none">XXXXX</a>
					<nav class="gn-menu-wrapper3 ">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<li>
									<a class="" href="#"><i class="fas fa-user"></i>XX</a>
								</li>
							</ul>
						</div><!-- /gn-scroller -->
					</nav>
				</li>
				<li>
					<a class="gn-icon-menu4" href="" style="display:none">XXXXX</a>
					<nav class="gn-menu-wrapper4 ">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<li>
									<a class="" href="#"><i class="fas fa-user"></i>XX</a>
								</li>
							</ul>
						</div><!-- /gn-scroller -->
					</nav>
				</li>
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
			  <div class="btnMobile" style="z-index:999;">
			    <div class="btnbtnMobile__content">
						<a href="update_qr_code.php" class="fab">
			      <i class="fas fa-qrcode icon-main-nav"></i><?=$show?>
					</a>
			    </div>
			  </div>
