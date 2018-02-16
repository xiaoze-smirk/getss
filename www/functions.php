<?php
/**
 * Created by IntelliJ IDEA.
 * User: lqs
 * Date: 18-2-16
 * Time: 下午6:09
 */

//获取文件内容
function getFileContent($filename){
    $uri = 'http://mof.trade/intro/'.$filename;
    $date =  date('Y-m-d');
    $file = $filename.$date;
    if(file_exists($file)){
        //文件存在，直接读取其内容
        return file_get_contents($file);
    }else{
        //文件不存在，下载之
        $filecontent = file_get_contents($uri);
        file_put_contents($file,$filecontent);
        return $filecontent;
    }
    return ‘’;
}

function getSSLink($file){
    $json = base64_decode($file);
    $p = '/ss:\/\/(.+?)"/';
    $rep = "aes-256-cfb:Sin1234qwer";

    $rep64 = base64_encode($rep);

    preg_match_all($p, $json, $match);
    $sslink = array();
    foreach( $match[0] as $str) {
        $start = strpos($str,'@')+1;
        $end = strpos($str,'"');

        $strs = substr($str,$start,$end-$start);

        $tmp = str_replace($rep,$rep64,$str);

        $tmp = str_replace('"',"",$tmp);
        $sslink["$strs"] = $tmp;
    }
    return $sslink;
}

function createUL($sslink){
    $text = "";

    foreach ($sslink as $key => $value) {

        $text .= "<li title='$value'>$key</li>";
    }
    return "<ul id='sslink'>$text</ul>";
}