<!DOCTYPE html>
<html lang="zh">
<head>
    <title>SSLink</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no"/>

    <script type="text/javascript" src="qrcode.min.js"></script>
    <style>
        ul {
            width: 80%;
            margin: 5px auto;
            padding: 0px;
            border-top: #0066cc 1px solid;
            border-left: #0066cc 1px solid;
            display: table;
        }

        li {
            float: left;
            width: 152px;
            height: 50px;
            list-style-type: none;
            border-right: #0066cc 1px solid;
            border-bottom: #0066cc 1px solid;
            text-align: center;
            line-height: 50px;
        }

        #qrcode {

            position: fixed;
            left: 0;
            top: 30%;
            margin-top: -2.5em;

        }
        textarea{
            display: block;
        }

    </style>
</head>
<body>
<div id="qrcode"><textarea id="sslink" cols="40" rows="3"></textarea>
    <textarea id="ssconfig" cols="40" rows="10"></textarea></div>
<?php
/**
 * Created by IntelliJ IDEA.
 * User: lqs
 * Date: 18-2-16
 * Time: 下午6:55
 */
require_once 'functions.php';
$filename = 'abc.txt';
if(isset($_GET['type'])){
    $type = $_GET['type'];
    if($type == 'mm') $filename = 'mm.txt';
}
$file = getFileContent($filename);
echo createUL(getSSLink($file));
?>


<script>
    function createjson(iport) {
        var arr = iport.split(":");

        var ip = arr[0];
        var port= arr[1];
        return {
            "server":ip,
            "server_port":port,
            "local_port":1080,
            "password":"Sin1234qwer",
            "timeout":60,
            "method":"aes-256-cfb"
        }
    }
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        width: 300,
        height: 300
    });

    function makeCode(val) {

        qrcode.makeCode(val);
    }

    var list = document.getElementsByTagName("li");
    for (var i = 0; i < list.length; i++) {
        list[i].onclick = function () {

            makeCode(this.title);
            this.style.color = "red";
            document.getElementById("sslink").innerHTML = this.title;
            document.getElementById("ssconfig").innerHTML = JSON.stringify(createjson(this.innerHTML));
        }
    }
</script>
<a href="index.php?type=mm" >mm</a>
</body>
</html>

