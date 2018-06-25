<?php
require_once 'functions.php';

$is_dingyue = isset($_GET['dingyue']) && $_GET['dingyue'] == '1';
$filename = 'abc.txt';
$file = getFileContent($filename);
$link = getSSLink($file);
$text = "";

foreach ($link as $item) {

    $text .= genNode($item, $is_dingyue) . "\n";

}
if ($is_dingyue) {
    echo base64_encode($text);
} else {
    echo $text;
}
