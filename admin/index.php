<!DOCTYPE html>
<?
require('../common/conn.php');

session_start();
//require_once('../lang/lang_conn.php');

$alert_msg = '';

if (isSet($_SESSION['user'])) {
    if ($_SESSION['user']['type'] == 'staff') {
        header('Location: main.php');
    }
}

if (isSet($_POST['login'])) {
	$staff_id = $_POST['staff_id'];
	$password = hash('sha512', $_POST['password']);

	$sql = $conn->prepare('SELECT * FROM staff WHERE staff_id=? AND password=?');
	$sql->bind_param('ss', $staff_id, $password);

	$sql->execute();

	$result = $sql->get_result();
	if ($record = $result -> fetch_assoc()) {
        if ($record['status'] == 0) {   // account disabled
            $alert_msg .= '此帳號已被停用！';
        } else {
            $_SESSION['user']['type'] = 'staff';
            $_SESSION['user']['account'] = $staff_id;
            $_SESSION['user']['position'] = $record['position_id'];
            $_SESSION['user']['eng_name'] = $record['eng_last_name'].', '.$record['eng_first_name'];
            $_SESSION['user']['chi_name'] = $record['chi_last_name'].$record['chi_first_name'];
            $_SESSION['lang'] = 'zh';
            header('Location: main.php');
        }
	} else $alert_msg .= '登入資料錯誤 ！';
}
?>
<html>
<head>
	<?require("common/headLogin.php");?>
</head>
<body>
  <h1 style="text-align:center;">WeProp 系 統 管 理 頁 面</h1>
  <div class="section">
		<div class="container">
			<div class="row full-height justify-content-center">
				<div class="col-12 text-center align-self-center py-5">
					<div class="section pb-5 pt-5 pt-sm-2 text-center">
						<!-- <h6 class="mb-0 pb-3"><span>登入</span><span>忘記密碼</span></h6> -->
			          	<input class="checkbox" type="checkbox" id="reg-log" name="reg-log">
			          	<!-- <label for="reg-log"></label> -->
						<div class="card-3d-wrap mx-auto">
							<div class="card-3d-wrapper">
								<div class="card-front">
									<div class="center-wrap">
										<div class="section text-center">
											<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
											<h4 class="mb-4 pb-3">登入</h4>
											<div class="form-group">
												<input type="text" name="staff_id" class="form-style" placeholder="帳號" id="logemail" autocomplete="off">
												<i class="input-icon uil uil-user"></i>
											</div>
											<div class="form-group mt-2">
												<input type="password" name="password" class="form-style" placeholder="密碼" id="logpass" autocomplete="off">
												<i class="input-icon uil uil-lock-alt"></i>
											</div>
											<input type="submit" name="login" value="登入" class="btn mt-4"/>
                    </form>
				      					</div>
			      					</div>
			      				</div>
								<!-- <div class="card-back">
									<div class="center-wrap">
										<div class="section text-center">
											<h4 class="mb-4 pb-3">忘記密碼</h4>
											<div class="form-group">
												<input type="text" name="staff_id" class="form-style" placeholder="帳號" id="logname" autocomplete="off">
												<i class="input-icon uil uil-user"></i>
											</div>
											<div class="form-group mt-2">
												<input type="email" name="email" class="form-style" placeholder="郵箱" id="logemail" autocomplete="off">
												<i class="input-icon uil uil-at"></i>
											</div>
											<a href="#" class="btn mt-4">提交</a>
				      					</div>
			      					</div>
			      				</div> -->
			      			</div>
			      		</div>
			      	</div>
		      	</div>
	      	</div>
	    </div>
	</div>
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
?>
</script>
</html>
<?$conn->close();?>
