<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <script src="html5-qrcode.min.js"></script>
</head>
<body>
    <h1> HTML5 QR Code scanner</h1>
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
            // handle on success condition with the decoded message
            alert(qrCodeMessage);
            html5QrcodeScanner.clear();
            // ^ this will stop the scanner (video feed) and clear the scan area.
        }

        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>
