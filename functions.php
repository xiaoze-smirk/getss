<?php

//获取文件内容
function getFileContent($filename){
    $uri = 'http://mof.trade/intro/'.$filename;
    $date =  date('Y-m-d');
    $file = $filename.$date;
    if(file_exists($file)){
        //文件存在
        return file_get_contents($file);
    }else{
        //文件不存在
        $filecontent = file_get_contents($uri);
        file_put_contents($file,$filecontent);
        return $filecontent;
    }
    return ‘’;
}

function getLink($file){
    $fileContent = base64_decode($file);
    $p = '~aes-256-cfb:Sin1234qwer((?!").)*~';
    preg_match_all($p, $fileContent, $match);
    $link = array();
    foreach ($match[0] as $item) {
        array_push($link, "ss://" . base64_encode($item));
    }
    return $link;
}
