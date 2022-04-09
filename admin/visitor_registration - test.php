

<?php
require('../common/conn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../lib/PHPMailer/src/Exception.php';
require '../lib/PHPMailer/src/PHPMailer.php';
require '../lib/PHPMailer/src/SMTP.php';

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}
$first_name = '';
$last_name ='';
$id_no = '';
if(isset($_POST['dataurll'])){
    $data = $_POST['dataurll'];
	$image_array_1 = explode(";", $data);
	$image_array_2 = explode(",", $image_array_1[1]);
	$data = base64_decode($image_array_2[1]);
	$file_name = time() . '.png';
    $out_name  = time();
    if(file_exists($out_name.".txt")){
        unlink($out_name.".txt");
    }
	file_put_contents('images/' .$file_name, $data);
    shell_exec('"C:\\Program Files\\Tesseract-OCR\\tesseract" "C:\\Service\\FTP\\TOMAKIZU\\fyp\\admin\\images\\'.$file_name.'" "C:\\Service\\FTP\\TOMAKIZU\\fyp\\admin\\images\\'.$out_name.'" -l chi_tra+eng');
    //shell_exec('"C:\\Program Files (x86)\\Tesseract-OCR\\tesseract" "C:\\xampp\\htdocs\\fyp\\admin\\images\\'.$file_name.'" "C:\\xampp\\htdocs\\fyp\\admin\\images\\'.$out_name.'" -l chi_tra+eng');

    $file_path = "./images/".$out_name.".txt";
    if(file_exists($file_path)){
    $f = fopen("images/".$out_name.".txt", 'r');
    $cursor = 0;
    fseek($f, $cursor++, SEEK_SET);
    $char = fgetc($f);
    $cardType='';
    while($cursor<50000){
        fseek($f, $cursor, SEEK_SET);
        $char = fgetc($f);
        $cardType = $cardType.$char;
        if($cardType==="V"||$cardType==="VT"||$cardType==="VTC"||
            $cardType==="VI"||$cardType==="VIC"||$cardType==="IVE"||$cardType==="IV"||
            $cardType==="H"||$cardType==="Hi"||$cardType==="Hig"||$cardType==="High"||
            $cardType==="I"||$cardType==="ID"||$cardType==="IDE"){

        }
        else{
            $cardType = '';
        }
        if($cardType === "VTC"||$cardType==="IVE"||$cardType==="VIC"||$cardType==="High"){
            break;
        }
        else if($cardType === "IDE"){
            break;
        }
        fseek($f, $cursor++, SEEK_SET);
        $char = fgetc($f);
    }
    if($cardType!=="VTC"&&$cardType!=="IDE"&&$cardType!=="VIC"&&$cardType!=="High"&&$cardType!=="IVE"){
        echo '<script language="javascript">';
        echo 'alert("can not detect correctly, please try again")';
        echo '</script>';
    }

    if($cardType==="IDE"){
        $cursor = -1;
        fseek($f, $cursor, SEEK_END);
        $char = fgetc($f);
        $idcheck = true;
        /**
         * Trim trailing newline chars of the file
         */
        while ($char!==")") {
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
            if($cursor<-1000){
                $idcheck = false;
                break;
            }
        }

        /**
         * Read until the start of file or first newline char
         */
        while (strlen($id_no)<12&&$idcheck) {
            /**
             * Prepend the new char
             */
            $id_no = $char . $id_no;
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
            if(strlen($id_no)>8 && $char === " "){
                break;
            }
        }

        $check = false;
        $namecheck = true;
        $cursor = 0;
        /**
         * Trim trailing newline chars of the file
         */
        while ($char === "\n" || $char === "\r"||$char !== "C") {
            fseek($f, $cursor++, SEEK_SET);
            $char = fgetc($f);
            if($cursor>1000){
                $namecheck = false;
                break;
            }
        }
        /**
         * Read until the start of file or first newline char
         */
        while ($char !== false && $char !== "\n" && $char !== "\r"&&$namecheck) {
            /**
             * Prepend the new char
             */
            $first_name = $first_name . $char;
            if($first_name==="C"||$first_name==="CA"||$first_name=="CAR"||$first_name==="CARD"){

            }
            else{
                $first_name = '';
            }
            if($first_name === "CARD"){
                $check = true;
            }
            fseek($f, $cursor++, SEEK_SET);
            $char = fgetc($f);
        }
        if($check){
            $here_cursor = $cursor;
            while ($char === "\n" || $char === "\r"||$char!==",") {
                fseek($f, $cursor++, SEEK_SET);
                $char = fgetc($f);
                if(is_numeric( $char)){
                    break;
                }
            }
            $first_name ="";
            $check2 = false;
            // while(!$check2){
            //     if (mb_strlen($char, "UTF-8") == strlen($char)){
            //         if(preg_match("/^[a-zA-Z]+$/", $char) == 1) {
            //             $check2 = true;
            //         }
            //         else{
            //             fseek($f, $cursor++, SEEK_SET);
            //             $char = fgetc($f);
            //         }
            //     }else {
            //         fseek($f, $cursor++, SEEK_SET);
            //         $char = fgetc($f);
            //     }
            //     if($cursor-$here_cursor==100){
            //         $here_cursor = $cursor;
            //         $check =false;
            //         break;
            //     }
            // }
            $newCursor = $cursor;
            while ($char !== "\n" && $char !== "\r") {
                fseek($f, $cursor--, SEEK_SET);
                $char = fgetc($f);
                if($char!==","){
                    $first_name = $char.$first_name;
                }

            }
            // while (!is_numeric( $char)) {
            //     $first_name = $first_name . $char;
            //     fseek($f, $cursor++, SEEK_SET);
            //     $char = fgetc($f);
            //     if($char===" "){
            //         break;
            //     }
            //     if(is_numeric( $char)||strlen($first_name)>20||$cursor-$here_cursor==100){
            //         $check =false;
            //         break;
            //     }
            // }
            $cursor=$newCursor+1;
            $spacechar ="";
            while (!is_numeric( $char)) {
                fseek($f, $cursor++, SEEK_SET);
                $last_name = $last_name . $char;
                $char = fgetc($f);
                if(is_numeric( $char)||strlen($first_name)>20||$cursor-$here_cursor==100){
                    $check =false;
                    break;
                }
                if($char === "\n"){
                    break;
                }
                if($char === " "){
                    $spacechar = $spacechar.$char;
                }else{
                    $spacechar="";
                }
                if(strlen($spacechar)>=3){
                    break;
                }
            }
        }
        // if(!$namecheck){
        //     $cursor = 0;
        //     $namecheck2 = true;
        //     $check44 = false;
        //     while ($char === "\n" || $char === "\r"||!is_numeric( $char)) {
        //         fseek($f, $cursor++, SEEK_SET);
        //         $char = fgetc($f);
        //         if($cursor>1000){
        //             $namecheck2 = false;
        //             break;
        //         }
        //     }
        //     while ($namecheck2) {
        //         /**
        //          * Prepend the new char
        //          */
        //         $last_name = $last_name . $char;
        //         if(is_numeric( $char)){
        //             $last_name = $last_name . $char;
        //         }
        //         else{
        //             $last_name = '';
        //         }
        //         if(strlen($last_name)>3){
        //             $check44 = true;
        //             break;
        //         }
        //         fseek($f, $cursor++, SEEK_SET);
        //         $char = fgetc($f);
        //         if($cursor>1000){
        //             break;
        //         }
        //     }
        //     if($check44){
        //         $last_name = '';
        //         $cursor-=3;
        //         while(strlen($last_name)<20){
        //             fseek($f, $cursor--, SEEK_SET);
        //             $char = fgetc($f);
        //             if($char===" "){
        //                 break;
        //             }
        //             else{
        //                 $last_name = $char.$last_name;
        //             }
        //         }
        //     }

        // }
    }
    if($cardType==="VTC"||$cardType==="IVE"||$cardType==="VIC"||$cardType==="High"){
        $vtcName ='';
        $vtcName2 ='';
        $cursor = 0;
        fseek($f, $cursor++, SEEK_SET);
        $char = fgetc($f);
        $vtcCheck = true;
        while (preg_match("/^[a-zA-Z]+$/", $char) !== 1) {
            fseek($f, $cursor++, SEEK_SET);
            $char = fgetc($f);
            if($cursor>10000){
                $vtcCheck = false;
                break;
            }
        }
        while ($char !== "\n" && $char !== "\r" && $vtcCheck !== false) {
            fseek($f, $cursor++, SEEK_SET);
            $vtcName = $vtcName . $char;
            if($char===" "){
                break;
            }else{
                $char = fgetc($f);
            }
            if($cursor>10000){
                break;
            }
        }
        $cursor-=2;
        while ($char !== "\n" && $char !== "\r" && $vtcCheck !== false) {
            $vtcName2 = $vtcName2 . $char;
            fseek($f, $cursor++, SEEK_SET);
            $char = fgetc($f);
            if($cursor>10000){
                break;
            }
        }
        $cursor = 0;
        $vtcId='';
        while($cursor<10000){
            fseek($f, $cursor++, SEEK_SET);
            $char = fgetc($f);
            if(is_numeric( $char)){
                $vtcId = $vtcId . $char;
            }
            else if(strlen($vtcId)>=9){
                break;
            }
            else{
                $vtcId='';
            }
        }


        $first_name = $vtcName;
        $last_name = $vtcName2;
        $id_no = $vtcId;
    }
    fclose($f);
}
}
?>
<html>
<head>
  <?require("common/head.php");?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
  <link rel="stylesheet" type="text/css" href="../common/css/form.css" />
	<script src="https://unpkg.com/cropperjs"></script>
    <script>
$(document).ready(function(){
	var $modal = $('#modal');
	var image = document.getElementById('sample_image');
	var cropper;
	$('#upload_image').change(function(event){
		var files = event.target.files;

		var done = function(url){
			image.src = url;
			$modal.modal('show');
		};

		if(files && files.length > 0)
		{
			reader = new FileReader();
			reader.onload = function(event)
			{
				done(reader.result);
			};
			reader.readAsDataURL(files[0]);
		}
	});

	$modal.on('shown.bs.modal', function() {
		cropper = new Cropper(image, {
			autoCropArea: 1,
			preview: '.preview'
		});
	}).on('hidden.bs.modal', function(){
		cropper.destroy();
   		cropper = null;
	});

	$('#rotate').click(function(){
		cropper.rotate(90);
	});

	$('#crop').click(function(){
		canvas = cropper.getCroppedCanvas({
			width:600,
			height:600
		});
		canvas.toBlob(function(blob){
			url = URL.createObjectURL(blob);
			console.log(url);
			var reader = new FileReader();
			reader.readAsDataURL(blob);
			var base64data = reader.result;
			document.getElementById('dataurl').value = canvas.toDataURL();
			// reader.onloadend = function(){
			// 	var base64data = reader.result;
			// 	$.ajax({
			// 		url:'upload.php',
			// 		method:'POST',
			// 		data:{image:base64data},
			// 		success:function(data)
			// 		{
			 			$modal.modal('hide');
			 			$('#uploaded_image').attr('src', url);
			// 		}
			// 	});
			// };
		});
		$( '#confirmPhoto' ).css('display', 'block');
	});
});
</script>
	<style>
	#sample_image {
		overflow: hidden;
		width: 500px;
		height: 500px;
	}
	</style>
</head>
<body>
    <center>
    <header><h3>PHP OCR Test</h3></header>
    <div class="grid">

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="form login" enctype="multipart/form-data">
     <div class="form__field">
       <input type="file"  id="upload_image" name="image" accept="image/*" capture="camera"/>
       <input id="dataurl" name="dataurll" type="hidden" value=""/>
     </div>
      <div class="form__field">
        <input type="hidden" name="visitor">
        <input id="login__username" type="text" name="last_name" class="form__input" placeholder="姓氏" value="<?php echo $last_name ?>">
      </div>
      <div class="form__field">
        <input id="login__password" type="text" name="first_name" class="form__input" placeholder="名字" value="<?php echo $first_name ?>">
      </div>
      <div class="form__field">
        <input id="login__password" type="text" name="id_no" class="form__input" placeholder="編號" value="<?php echo $id_no ?>">
      </div>
      <div class="form__field">
        <input type="submit">
        <input type="reset">
      </div>

    </form>

  </div>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
    <input type="file"  id="upload_image" name="image" accept="image/*" capture="camera"/>
    <input id="dataurl" name="dataurll" type="hidden" value=""/>
    <input type="submit" id="confirmPhoto" style="display:none"/>
    </form>
    </center>
    <div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="hidden" name="visitor">
            <input type="text" name="last_name" value="<?php echo $last_name ?>">
            <input type="text" name="first_name" value="<?php echo $first_name ?>">
            <input type="text" name="id_no" value="<?php echo $id_no ?>">
            <input type="submit"/>
            <input type="reset"/>
        </form>
    </div>
    <img src="" id="uploaded_image" />
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
			  	<div class="modal-dialog modal-lg" role="document">
			    	<div class="modal-content">
			      		<div class="modal-header">
			        		<h5 class="modal-title">Crop Image Before Upload</h5>
			        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          			<span aria-hidden="true">×</span>
			        		</button>
			      		</div>
			      		<div class="modal-body">
			        		<div class="img-container">
			            		<div class="row">
			                		<div class="col-md-8">
			                    		<img src="" id="sample_image" />
			                		</div>
			                		<div class="col-md-4">
			                    		<p value="32">please</p>
			                		</div>
			            		</div>
			        		</div>
			      		</div>
			      		<div class="modal-footer">
			      			<button type="button" id="crop" class="btn btn-primary">Crop</button>
							  <button type="button" id="rotate">rotate</button>
			        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			      		</div>
			    	</div>
			  	</div>
			</div>
</body>
</html>
<?php
if (isSet($_POST['visitor'])) {
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $idno = $_POST['id_no'];
    $empty = "empty";
    $alert_msg='';
    $sql = $conn->prepare('INSERT INTO visitor (email,hash,staff_id,eng_first_name, eng_last_name,id_no) VALUES (?, ?, ?, ?, ?, ?)');
    $sql->bind_param('ssssss',$empty,$empty,$_SESSION['user']['account'], $fname, $lname,$idno);
    $sql->execute();
    if ($sql->affected_rows == 1) {
        $alert_msg = '登記成功！';
    } else {
        $alert_msg = "登記失敗！";
    }
}
if ($alert_msg <> '') echo "alert('$alert_msg');";
?>
