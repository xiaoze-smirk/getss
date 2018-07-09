<?php

//获取文件内容
function getFileContent($filename)
{
    $uri = 'http://mof.trade/intro/' . $filename;
    $date = date('Y-m-d');
    $file = $filename . $date;
    if (file_exists($file)) {
        //文件存在
        return file_get_contents($file);
    } else {
        //文件不存在
        $filecontent = file_get_contents($uri);
        file_put_contents($file, $filecontent);
        return $filecontent;
    }
    return ‘’;
}
function getSSLink($file)
{
    $fileContent = base64_decode($file);
//    echo $fileContent;
    $p = '~ss://((?!").)*~';//~是分界符
    preg_match_all($p, $fileContent, $match);
    $link = array();
    foreach ($match[0] as $item) {
        array_push($link, $item);
    }
    return $link;
}
function genNode($sslink, $is_SSR = false)
{
    str_replace($sslink, "ss://", "");
    $ss = explode("@", $sslink);
    $url = explode(":", $ss[1]);
    $ip = $url[0];
    $port = $url[1];
    $node = $is_SSR ? new SSR_Node($ip, $port) : new SS_Node($ip, $port);
    return $node->genLink();
}
//genNode("ss://aes-256-cfb:Sin1234qwer@138.68.156.22:8385");
interface Inode
{
    function genLink();
}
class SS_Node implements Inode
{
    private $host;//服务器IP
    private $port;//端口
    private $method;//加密方法
    private $password;//密码
    private $link; //连接
    /**
     * SS_Node constructor.
     * @param $host
     * @param $port
     * @param $method
     * @param $password
     */
    public function __construct($host, $port, $method = "aes-256-cfb", $password = "Sin1234qwer")
    {
        $this->host = $host;
        $this->port = $port;
        $this->method = $method;
        $this->password = $password;
        $this->link = "$method:$password@$host:$port";
    }
    function genLink()
    {
        return "ss://" . urlsafe_b64encode($this->link);
    }
}
class SSR_Node implements Inode
{
    private $host;//服务器IP
    private $port;//端口
    private $method;//加密方法
    private $password;//密码
    private $link; //连接
    private $protocol;//协议
    private $obfs;//混淆方式
    /**
     * SSR_Node constructor.
     * @param $host
     * @param $port
     * @param $method
     * @param $password
     * @param $protocol
     * @param $obfs
     */
    public function __construct($host, $port, $method = "aes-256-cfb", $password = "Sin1234qwer", $protocol = "origin", $obfs = "plain")
    {
        $this->host = $host;
        $this->port = $port;
        $this->method = $method;
        $this->password = $password;
        $this->protocol = $protocol;
        $this->obfs = $obfs;
        $this->link = "$host:$port:$protocol:$method:$obfs:" . urlsafe_b64encode($password) . "/?group=" . urlsafe_b64encode("free");
    }
    function genLink()
    {
        return "ssr://" . urlsafe_b64encode($this->link);
    }
}
//
//$node1 = new SSR_Node("127.0.0.1", "8080");
//echo $node1->genLink();
/**
 * URL base64解码
 * '-' -> '+'
 * '_' -> '/'
 * 字符串长度%4的余数，补'='
 * @param unknown $string
 */
function urlsafe_b64decode($string)
{
    $data = str_replace(array('-', '_'), array('+', '/'), $string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}
/**
 * URL base64编码
 * '+' -> '-'
 * '/' -> '_'
 * '=' -> ''
 * @param unknown $string
 */
function urlsafe_b64encode($string)
{
    $data = base64_encode($string);
    $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
    return $data;
}


$is_dingyue = isset($_GET['dingyue']);
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
