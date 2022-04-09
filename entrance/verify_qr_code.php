<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>WeProp門禁系統</title>
    <script type="text/javascript" src="etc/html5-qrcode.min.js"></script>
    <script type="text/javascript" src="etc/jquery-3.5.1.js"></script>
    <link type="text/css" rel="stylesheet" href="etc/styles.css">
    <link rel="stylesheet" type="text/css" href="etc/normalize.css" />
    <link rel="stylesheet" type="text/css" href="etc/demo.css" />
    <link rel="stylesheet" type="text/css" href="etc/component.css" />
    <link rel="stylesheet" type="text/css" href="etc/mobile.css" />
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
</head>
<script type="text/javascript">
    $(document).ready(function() {
        $('#startScan').on('click', function() {
            console.log('startScan');
        });
        $('#postRequest').ready(function() {
            $('#startScan').trigger('click');
        });
        $('#requestCamera').on('click', function() {
            console.log('requestCamera');
        });
        $('#requestCamera').trigger('click');

    });
</script>
<body>
<div class="container" id="result"><header><h1>請掃描用戶二維碼</h1></header></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12" style="text-align: center;margin-bottom: 20px;">
                <div id="reader" style="display: inline-block;"></div>
                <div class="empty"></div>
                <div id="scanned-result"></div>
            </div>
        </div>
    </div>
    <script>
    var html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);

    function onScanSuccess(qrCodeMessage) {
        $('#result').html("<header><h2>請稍候 Please Wait</h2></header>");
        // handle on success condition with the decoded message
        $.ajax({
            type: "POST",
            url: "etc/ajax.php?msg=" + qrCodeMessage,
            dataType: "json",
            success: function(result) {
                console.log(result);
                var alert_msg = '';
                if (result.type == 'resident') {
                    if (result.record.lang == 'en') {
                        alert_msg = '<header><h2>Welcome back, ' + result.record.eng_first_name + ' ' + result.record.eng_last_name + '.\nPlease enter the building.</h2></header>';
                    } else if (result.record.chi_first_name == null || result.record.chi_last_name == null) {
                        alert_msg = '<header><h2>歡迎回來，' + result.record.eng_last_name + ' ' + result.record.eng_first_name + '，請進入大廈。</h2></header>';
                    } else {
                        alert_msg = '<header><h2>歡迎回來，' + result.record.chi_last_name + ' ' + result.record.chi_first_name + '，請進入大廈。</h2></header>';
                    }
                } else if (result.type == 'visitor') {
                    if (result.lang == 'en') {
                        alert_msg = '<header><h2>Welcome, ' + result.record.eng_first_name + ' ' + result.record.eng_last_name + '.\nPlease enter the building.</h2></header>';
                    } else {
                        alert_msg = '<header><h2>歡迎光臨，' + result.record.eng_first_name + ' ' + result.record.eng_last_name + '，請進入大廈。</h2></header>';
                    }
                } else {
                    alert_msg = '<header><h2>無效的二維碼 Invalid QR Code</h2></header>';
                }
                $('#result').html(alert_msg);
                setTimeout(function() { $('#result').html("<header><h2>請掃描二維碼 Please Scan QR Code</h2></header>"); }, 5000);
            },
            error: function(err) {}
        });
    }

    html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>
