<?php
require_once 'functions.php';
$filename = 'abc.txt';
$file = getFileContent($filename);
$link = getLink($file);
$text = "";

if ($_GET['dingyue'] == '1') {
    foreach ($link as $item){
    $text .="$item?group=ZnJlZQ\n";
    }
    echo base64_encode($text);
} else {
    foreach ($link as $item){
    $text .="$item\n";
    }
    echo $text;
}
