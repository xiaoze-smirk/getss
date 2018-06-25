<?php
require_once 'functions.php';
$filename = 'abc.txt';
$file = getFileContent($filename);
$link = getLink($file);
$text = "";
foreach ($link as $item){
    $text .="$item\n";
}
if ($_GET['dingyue'] == '1') {
    echo base64_encode($text);
} else {
    echo $text;
}
