<?php
	$start_month = 8;
	$start_year = 2020;
	$recent_month  = (int)date("m");
	$recent_year  = (int)date("Y");
	require('../common/conn.php');
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require '../lib/PHPMailer/src/Exception.php';
	require '../lib/PHPMailer/src/PHPMailer.php';
	require '../lib/PHPMailer/src/SMTP.php';

	session_start();
	$alert_msg = '';

	if (!isSet($_SESSION['user'])) {
		header('Location: index.php');
	} else if ($_SESSION['user']['type'] != 'staff') {
		header('Location: index.php');
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<style>
		.demo{
			background-color: #fff;
			width: 50%;
			height: 900px;
			float: right;
			/* margin: auto; */
		}
		.demo-container{
			background-color: #fff;
			width: 100%;
			height: 900px;
			float: right;
			/* margin: auto; */
		}
		.red{
			background-color: red;
			width: 50px;
			height: 50px;
		}
		.left{
			background-color: #fff;
			width: 50%;
			height: 900px;
			float: left;
			/* margin: auto; */
		}
		#leftRight{
			width: 100%;
			height: 1000px;
			margin-top: -100px;
      margin-bottom: -200px;
		}
		#leftRight >tbody > tr > td{
			width: 1000px;
			font-size: 20px;
			color: #FFF;
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
    center > table{
      border-collapse: collapse;
      /* table-layout: fixed; */
    }
    center > table > thead > tr > th{
      padding: 10px;
      border: 1px solid #000;
      background-color: #ddd;
			width: 200px;
    }
		center > table > thead > tr > td {
			padding: 10px;
      border: 1px solid #000;
		}
		select {
			font-size: 16px;
		}
	</style>
</head>
<body style="background: #34495e;">
	<center>
	<table border="1">
		<thead>
			<tr>
				<th>圖表主題</th>
				<th>圖表類型</th>
				<th>期間</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<center>
					<select name="chartTheme" id="chartTheme">
						<option value="visitor" selected>訪客人次</option>
						<option value="opinion">住戶意見</option>
					</select>
				</center>
				</td>
				<td>
					<center>
					<select name="typeChart" id="typeChart">
						<option value="column" selected>棒形</option>
						<option value="line">折線</option>
						<option value="pie">餅狀</option>
					</select>
					</center>
					</td>
					<td>
						<center>
						<select name="start_period" id="start_period">
						<?php
							$half_month = $recent_month;
							$half_year = $recent_year;
							for($i=0;$i<6;$i++){
								if($half_month==0){
									$half_month=12;
									$half_year = $half_year-1;
								}
								$half_month -= 1;
							}
							if($half_month<10){
								$half_my = $half_year."/0".$half_month;
							}else{
								$half_my = $half_year."/".$half_month;
							}
							while($start_month != $recent_month || $start_year != $recent_year){
								$start_month+=1;
								if($start_month<10){
									$date = $start_year."/0".$start_month;
								}
								else{
									$date = $start_year."/".$start_month;
								}
								if($date==$half_my){
									echo "<option value=".$date." selected>".$date."</option>";
								}else{
									echo "<option value=".$date.">".$date."</option>";
								}
								if($start_month==12){
									$start_month=0;
									$start_year+=1;
								}
							}
						?>
						</select>
						至
						<select name="end_period" id="end_period">
						<?php
						$half_month-=1;
						if($half_month<0){
							$half_month=0;
						}
						while($half_month != $recent_month || $half_year != $recent_year){
							$half_month+=1;
							if($half_month<10){
								$date = $half_year."/0".$half_month;
							}
							else{
								$date = $half_year."/".$half_month;
							}
							if($half_month==$recent_month){
								echo "<option value=".$date." selected>".$date."</option>";
							}else{
								echo "<option value=".$date.">".$date."</option>";
							}
							if($half_month==12){
								$half_month=0;
								$half_year+=1;
							}
						}
						?>
						</select>
						</center>
					</td>
				</tr>
			</tbody>
</table>
</center>
	<table id="leftRight">
		<tr>
			<td>
				<p id="contents"></p>
			</td>
			<td>
				<div id="chartContainer1" style="height: 50%; width: 100%; border:none;"></div>
			</td>
		</tr>
	</table>
	<button id="print" style="display:none;">下載pdf</button>
	<center><img src="../common/img/download.gif" class="insertIcon" id="insertIcon" style="border-radius: 0.55rem;cursor:pointer;"></center>
</body>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>
<script src="https://cdn.bootcss.com/html2canvas/0.5.0-beta4/html2canvas.js"></script>
<script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script src="https://canvasjs.com/assets/script/jquery-ui.1.11.2.min.js"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
<script>
	$(document).ready(function() {
		$("#print").click(function(){
		download();
		});
		$("#typeChart,#chartTheme,#end_period").change(function(){
			getData($("#typeChart").val(),$("#chartTheme").val(),$("#start_period").val(),$("#end_period").val());
		});
		$("#start_period").change(function(){
			date = $("#start_period").val().split("/");
			year = parseInt(date[0]);
			month = parseInt(date[1]);
			date = new Date();
			current_month = parseInt(date.getMonth())+1;
			current_year  = parseInt(date.getFullYear());
			$("#end_period option").remove();
			month=month-1;
			if(month<0){
				month=0;
			}
			while( month != current_month || year != current_year){
				month=month+1;
				if(month==13){
					month=1;
					year=year+1;
				}
				if(month<10){
					full_date = year + "/0" + month;
				}
				else{
					full_date = year + "/" + month;
				}
				$('#end_period').append($("<option></option>").attr("value", full_date).text(full_date));
			}
			getData($("#typeChart").val(),$("#chartTheme").val(),$("#start_period").val(),$("#end_period").val());
		});
		const realFileBtn = document.getElementById("print");
    const customBtn = document.getElementById("insertIcon");
    customBtn.addEventListener('click',function(){
      realFileBtn.click();
    });
	});

</script>
<script>
   function download(){
	   var element = $("#leftRight");
		 $("#contents").css("color","black");
	   var w = element.width();    // get element width
	   var h = element.height();    // get element height
	   var offsetTop = element.offset().top;
	   var offsetLeft = element.offset().left;
	   var canvas = document.createElement("canvas");
	   var abs = 0;
	   var win_i = $(window).width();    // get window width include scroll bar
	   var win_o = window.innerWidth;    //get window width
	   if(win_o>win_i){
	     abs = (win_o - win_i)/2;
	   }
	   canvas.width = w * 2;    // 2 time the canvas
	   canvas.height = h * 2;
	   var context = canvas.getContext("2d");
	   context.scale(2, 2);
	   context.translate(-offsetLeft-abs,-offsetTop);
	   html2canvas(element).then(function(canvas) {
	    var contentWidth = canvas.width;
	    var contentHeight = canvas.height;
	    var pageHeight = contentWidth / 592.28 * 841.89;
	    var leftHeight = contentHeight;
	    var position = 0;
	    //set to a4 size[595.28,841.89]
	    var imgWidth = 595.28;
	    var imgHeight = 592.28/contentWidth * contentHeight;
	    var pageData = canvas.toDataURL('image/png', 1.0);
	    var pdf = new jsPDF('', 'pt', 'a4');
	    if (leftHeight < pageHeight) {
	    pdf.addImage(pageData, 'PNG', 0, 0, imgWidth, imgHeight);
	    } else {    // new page
	        while(leftHeight > 0) {
	            pdf.addImage(pageData, 'PNG', 0, position, imgWidth, imgHeight)
	            leftHeight -= pageHeight;
	            position -= 841.89;
	            //Avoid empty page
	            if(leftHeight > 0) {
	              pdf.addPage();
	            }
	        }
	    }
	    pdf.save('報告.pdf');
	  })
		$("#contents").css("color","white");
	}
	window.onload = function () {
		date = new Date();
		l_month = c_month = parseInt(date.getMonth())+1;
		l_year  = c_year  = parseInt(date.getFullYear());
		first_point = '';
		late_point = '';
		for(i=6;i>0;i--){
			l_month = l_month-1;
			if(l_month==0){
				l_month = 12;
				l_year  = l_year-1;
			}
		}
		if(l_month<10){
			first_point = l_year + "/0" + l_month;
		}else{
			first_point = l_year + "/" + l_month;
		}
		if(c_month<10){
			late_point = c_year + "/0" + c_month;
		}else{
			late_point = c_year + "/" + c_month;
		}
		getData('column','visitor',first_point,late_point);
    }
	function getData(type,theme,start,end){
		$.ajax({
            type: "POST",
            url: "ajax/get_information.php?theme="+theme+"&start_day="+start+"&end_day="+end,
            dataType: "JSON",
            success: function(result) {
				var dataPoints = [];
				for (var i = 0; i < result.length; i++) {
					dataPoints.push({label: result[i].date,y: result[i].numbers});
                }
				var content = getContent(theme,dataPoints);
				createChart(type,content,dataPoints);
				mainContent(content,dataPoints);
            },
            error: function(err) {alert('n');}
        });
	}
	function getContent(theme,dataPoints){
		mainParagraph ='';
		total = 0;
		if(theme=='visitor'){
			object = "訪客";
			title = "訪客人數統計";
			titleY = "人數";
			suffixY= "位";
			toolTipContent = "{y}位";
			sentence = ["檢查登記是否無遺留","要加倍注意大門保安","應繼續謹守崗位","多加留意"];
		}else if(theme=='opinion'){
			object = "意見";
			title = "意見收集及處理量";
			titleY = "宗數";
			suffixY= "宗";
			toolTipContent = "{y}宗";
			sentence = ["可證管理良好","要加緊改善","處理尚可","多加留意"];
		}
		content = [object,sentence,title, titleY,suffixY,toolTipContent];
		return content;
	}
	function mainContent(content,dataPoints){
		for(i=0;i<Object.values(dataPoints).length;i++){
			total += dataPoints[i].y;
		}
		mainParagraph = "跟據統計,";
		if(Object.values(dataPoints).length>0){
			mainParagraph += "過往"+ Object.values(dataPoints).length + "個月一共有" + total + content[4] + content[0] + "。<br>";
		}else{
			mainParagraph += "此期間內沒有" + content[0];
		}

		subParagraph = '';
		dataSet = dataPoints;
		if(Object.values(dataPoints).length>3){
			first_half  = 0;
			second_half = 0;
			half = parseInt(Object.values(dataPoints).length/2);
			if(Object.values(dataPoints).length%2!=0){
				half+=1;
			}
			for(x = 0;x<half;x++){
				first_half += dataPoints[x].y;
			}
			if(Object.values(dataPoints).length%2!=0){
				half-=1;
			}
			for(x = (Object.values(dataPoints).length-1); x>=half;x--){
				second_half += dataPoints[x].y;
			}
			if(first_half-second_half >=30){
				subParagraph +="下半部的" + content[0] + "大幅高於上半部," + content[1][0];
			}else if(second_half-first_half>=30){
				subParagraph +="下半部的" + content[0] + "大幅高於上半部," + content[1][1];
			}else if(first_half-second_half >=20){
				subParagraph +="上半部的" + content[0] + "高於下半部,有下降的趨勢,"+ content[1][2];
			}else if(second_half-first_half>=20){
				subParagraph +="下半部的" + content[0] + "高於上半部,有上升的趨勢,"+ content[1][3];
			}else if(first_half-second_half >=10){
				subParagraph +="上半部的" + content[0] + "略高於下半部";
			}else if(second_half-first_half>=10){
				subParagraph +="下半部的" + content[0] + "略高於上半部";
			}else{
				subParagraph +="下半部與上半部相差不大";
			}
		}
		if(Object.values(dataPoints).length>1){
			dataSet.sort(function(a, b) {
				return a.y - b.y;
			});
			max =[];
			max.push(dataSet[(Object.values(dataSet).length)-1]);
			counter = 1;
			if(Object.values(dataSet).length>1){
				while(dataSet[(Object.values(dataSet).length)-counter].y == dataSet[(Object.values(dataSet).length)-(counter+1)].y){
					max.push(dataSet[(Object.values(dataSet).length)-(counter+1)]);
					counter++;
				}
			}
			mainParagraph += "最多" + content[0] + "的月份為" ;
			for(i=0;i<Object.values(max).length;i++){
				maxs = (max[i].label).split("/");
				if(maxs[1]<10){
					maxs_month = maxs[1].split("");
					maxs[1] = maxs_month[1];
				}
				mainParagraph += maxs[0] + "年" + maxs[1] + "月,";
			}
			mainParagraph += "有" + max[0].y + content[4] + content[0] + "。";
			min = [dataSet[0]];
			counter = 0;
			if(Object.values(dataSet).length>1){
				while(dataSet[counter].y == dataSet[(counter+1)].y){
					min.push(dataSet[(counter+1)]);
					counter++;
				}
			}
			mainParagraph += "最少" + content[0] + "的月份為" ;
			for(i=0;i<Object.values(min).length;i++){
				mini = (min[i].label).split("/");
				if(mini[1]<10){
					mini_month = mini[1].split("");
					mini[1] = mini_month[1];
				}
				mainParagraph += mini[0] + "年" + mini[1] + "月,";
			}
			mainParagraph += "有" + min[0].y + content[4] + content[0] + "。<br>";
			if(Object.values(dataPoints).length>3){
				mainParagraph +=subParagraph;
			}
		}
		$('#contents').html(mainParagraph);
	}

	function createChart(type,content,dataPoints){
		trueFalse = false;
		if(type=='pie'){
			trueFalse = true;
		}
		var options1 = {
			animationEnabled: true,
			title: {
				text: content[2]
			},
			axisY:{
				interval: 5,
				title: content[3],
				suffix: content[4]
			},
			axisX:{
				title: "月份",
				suffix: ""
			},
			data: [{
				toolTipContent: "<b>{label}</b>:"+content[5],
				legendText: "{label}",
				type: type, //change it to line, area, bar, pie,column, etc
				indexLabel: content[5],
				showInLegend: trueFalse,
				dataPoints: dataPoints
				}]
    	};
    	$("#chartContainer1").CanvasJSChart(options1);
	}
</script>
</html>
