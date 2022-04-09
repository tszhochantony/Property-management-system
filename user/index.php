<!DOCTYPE html>
<?
require('../common/conn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../lib/PHPMailer/src/Exception.php';
require '../lib/PHPMailer/src/PHPMailer.php';
require '../lib/PHPMailer/src/SMTP.php';

session_start();
//require_once('../lang/lang_conn.php');

$alert_msg = '';

if (isSet($_SESSION['user'])) {
    if ($_SESSION['user']['type'] == 'resident') {
        header('Location: main.php');
    }
}

if (isSet($_POST['login'])) {
	$email = $_POST['email'];
	$password = hash('sha512', $_POST['password']);

	$sql = $conn->prepare('SELECT * FROM resident WHERE email=? AND password=?');
	$sql->bind_param('ss', $email, $password);

	$sql->execute();

	$result = $sql->get_result();
	if ($record = $result -> fetch_assoc()) {
        if ($record['status'] == 0) { // account disabled
            $alert_msg .= '此帳號已被停用！如有疑問，請聯絡管理處。\nThis account has been disabled. For enquires, please contact managment office.';
        } else {
            $_SESSION['user']['type'] = 'resident';
            $_SESSION['user']['account'] = $email;
            //$_SESSION['user']['position'] = $record['position_id'];
            $_SESSION['user']['eng_name'] = $record['eng_last_name'].', '.$record['eng_first_name'];
            $_SESSION['user']['chi_name'] = $record['chi_last_name'].$record['chi_first_name'];
            $_SESSION['user']['lang'] = $record['lang'];
            $_SESSION['lang'] = $record['lang'];
            $_SESSION['user']['is_owner'] = isOwner($email);
            header('Location: main.php');
        }
	} else $alert_msg .= '登入資料錯誤！\nInvalid Login Information!';
} else if (isSet($_POST['reset_pw'])) {
    $email = $_POST['email'];
    $sql = $conn->prepare('SELECT * FROM resident WHERE email=?');
	$sql->bind_param('s', $email);

	$sql->execute();

	$result = $sql->get_result();
	if ($record = $result -> fetch_assoc()) {
        if ($record['status'] == 0) { // account disabled
            $alert_msg .= '此帳號已被停用！如有疑問，請聯絡管理處。\nThis account has been disabled. For enquires, please contact managment office.';
        } else if ($record['status'] == 2) { // account is temp
            resendPreRegEmail($record['email'], $record['hashed_email']);
            $alert_msg .= '住戶帳號申請電郵已重新發送至'.$record['email'].'，敬請查收。\n An email regarding the account registration has been resent to '.$record['email'].'.';
        } else {
            $name = '';
            if (is_null($record['chi_last_name']) || is_null($record['chi_first_name']) || $record['lang'] == 'en') {
                $name = $record['eng_last_name'].' '.$record['eng_first_name'];
            } else {
                $name = $record['chi_last_name'].' '.$record['chi_first_name'];
            }
            sendResetPasswordEmail($record['email'], $record['hashed_email'], $name, $record['lang']);
            $alert_msg .= '重設住戶密碼電郵已重新發送至'.$record['email'].'，敬請查收。\n An email regarding the account registration has been resent to '.$record['email'].'.';
        }
	} else $alert_msg .= '錯誤：此電郵地址不存在！\nError: The email does not exist!';
}

function sendResetPasswordEmail($email, $hashed_email, $name, $lang) {
    $email_title = '';
    $email_context = '';
    if ($lang == 'zh') {
        $email_title = '重設WeProp住戶密碼';
        $email_context .= '<p>親愛的 '.$name.'：</p>';
        $email_context .= '<p>閣下收到此電郵是因為閣下申請重新設定WeProp住戶密碼。若閣下並未申請此項變更，可以忽略此電郵。</p>';
        $email_context .= '<p>若要重設閣下的WeProp住戶密碼，請遵循下方的連結：</p>';
        $email_context .= '<a href="https://tomakizu.wtf/fyp/user/reset_password.php?id='.$hashed_email.'">https://tomakizu.wtf/fyp/user/reset_password.php?id='.$hashed_email.'</a>';
        $email_context .= '<p>WeProp團隊謹啟</p>';
    } else {
        $email_title = 'WeProp Resident Account\'s Password Reset';
        $email_context .= '<p>Dear '.$name.'：</p>';
        $email_context .= '<p>We got a request to reset your WeProp account\'s password. If you did not initiate this password reset request, you can safely ignore this email.</p>';
        $email_context .= '<p>If you like to reset your WeProp account\'s password, please proceed to the following link:</p>';
        $email_context .= '<a href="https://tomakizu.wtf/fyp/user/reset_password.php?id='.$hashed_email.'">https://tomakizu.wtf/fyp/user/reset_password.php?id='.$hashed_email.'</a>';
        $email_context .= '<p>Yours sincerely,</p>';
        $email_context .= '<p>The WeProp Team</p>';
    }
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = '465';
    $mail->isHTML();
    $mail->Username = 'fyp.202107@gmail.com';
    $mail->Password = 'daisiirsdo';
    $mail->SetFrom('no-reply@weprop.com.hk', 'WeProp');
    $mail->Subject = $email_title;
    $mail->Body = $email_context;
    $mail->AddAddress($email);

    $mail->Send();
}

function resendPreRegEmail($email, $hashed_email) {
    $email_context = '<p>親愛的 '.$email.'：</p>';
    $email_context .= '<p>歡迎閣下成為WeProp旗下屋苑的一份子，請按以下連結完成註冊手續：</p>';
    $email_context .= '<a href="https://tomakizu.wtf/fyp/user/pre_register.php?lang=zh&id='.$hashed_email.'">https://tomakizu.wtf/fyp/user/pre_register.php?id='.$hashed_email.'</a>';
    $email_context .= '<p>如閣下並非WeProp旗下屋苑住戶，請無須理會此則信息。</p>';
    $email_context .= '<p>WeProp團隊謹啟</p>';
    $email_context .= '<hr />';
    $email_context .= '<p>Dear '.$email.',</p>';
    $email_context .= '<p>Welcome to WeProp\'s property. Please proceed to the following link for the account registration: </p>';
    $email_context .= '<a href="https://tomakizu.wtf/fyp/user/pre_register.php?lang=en&id='.$hashed_email.'">https://tomakizu.wtf/fyp/user/pre_register.php?id='.$hashed_email.'</a>';
    $email_context .= '<p>If you are not the resident of WeProp\'s property, please ignore this email.</p>';
    $email_context .= '<p>Yours sincerely,</p>';
    $email_context .= '<p>The WeProp Team</p>';

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = '465';
    $mail->isHTML();
    $mail->Username = 'fyp.202107@gmail.com';
    $mail->Password = 'daisiirsdo';
    $mail->SetFrom('no-reply@weprop.com.hk', 'WeProp');
    $mail->Subject = 'WeProp住戶帳號申請 / WeProp Resident Account Registration';
    $mail->Body = $email_context;
    $mail->AddAddress($email);

    $mail->Send();
}

function isOwner($email) {
    $sql = $GLOBALS['conn']->prepare('SELECT * FROM owner WHERE user_email=?');
    $sql->bind_param('s', $email);

    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) return '1';
    else return '0';
}
?>
<html>
<head>
	<?require("common/headLogin.php");?>
</head>
<body>
  <h1 style="text-align:center;">WeProp住戶系統 WeProp Resident System</h1>
  <div class="section">
		<div class="container">
			<div class="row full-height justify-content-center">
				<div class="col-12 text-center align-self-center py-5">
					<div class="section pb-5 pt-5 pt-sm-2 text-center">
						<center><h6 class="mb-0 pb-3"><span style="text-transform: unset !important;margin-left: 55px;text-align: left;display:inline-block;">Login<br/>登入</span><span style="text-transform: unset !important;text-align: left;display:inline-block;">Forget Password<br/>忘記密碼</span></h6></center>
			          	<input class="checkbox" type="checkbox" id="reg-log" name="reg-log">
			          	<label for="reg-log"></label>
						<div class="card-3d-wrap mx-auto">
							<div class="card-3d-wrapper">
								<div class="card-front">
									<div class="center-wrap">
										<div class="section text-center">
											<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
											<h4 class="mb-4 pb-3">登入 Login</h4>
											<div class="form-group">
												<input type="text" name="email" class="form-style" placeholder="電郵 Email" id="logemail" autocomplete="off">
												<i class="input-icon uil uil-user"></i>
											</div>
											<div class="form-group mt-2">
												<input type="password" name="password" class="form-style" placeholder="密碼 Password" id="logpass" autocomplete="off">
												<i class="input-icon uil uil-lock-alt"></i>
											</div>
											<input type="submit" name="login" value="登入 Login" class="btn mt-4"/>
                    </form>
				      					</div>
			      					</div>
			      				</div>
								<div class="card-back">
									<div class="center-wrap">
										<div class="section text-center">
                                            <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
                                                <h4 class="mb-4 pb-3">忘記密碼 Forget Password</h4>
                                                <div class="form-group">
                                                    <input type="text" name="email" class="form-style" placeholder="電郵 Email" id="logemail" autocomplete="off">
                                                    <i class="input-icon uil uil-at"></i>
                                                </div>
                                                <input type="submit" name="reset_pw" value="提交 Submit" class="btn mt-4"/>
                                            </form>
				      					</div>
			      					</div>
			      				</div>
			      			</div>
			      		</div>
			      	</div>
		      	</div>
	      	</div>
	    </div>
	</div>
<!-- 舊登入 -->
	<!-- <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<p>WeProp 住 戶 系 統</p>
		<table border="0">
			<tr>
				<td>Email: </td>
				<td><input type="text" name="email" /></td>
			</tr>
			<tr>
				<td>Password: </td>
				<td><input type="password" name="password" /></td>
			</tr>
		</table>
		<p><input type="submit" name="login" value="登入" /></p>
	</form> -->
</body>
<script type="text/javascript">
<?php
if ($alert_msg <> '') echo "alert('$alert_msg');";
?>
</script>
</html>
<?$conn->close();?>
