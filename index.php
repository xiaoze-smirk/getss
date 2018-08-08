<?php
//获取文件内容

include("getfile.php");

class GetSS
{
    var $ipl;

    /**
     * GetSS constructor.
     * @param $ipLocation
     */
    public function __construct()
    {
        $this->ipl = new ipLocation("qqwry.dat");
    }


    function getFileContent($filename, $update = false)
    {
        $uri = 'http://mof.trade/intro/' . $filename;
        $date = date('Y-m-d');
        $file = $filename;
        if ($update) {
            $filecontent = file_get_contents($uri);
            file_put_contents($file, $filecontent);
            //文件保存失败
            if (!file_exists($file)) {
                return "";
            }
        }
        if (file_exists($file)) {
            //文件存在
            return file_get_contents($file);
        }
        //文件不存在
        if (!file_exists($file)) {
            return getFileContent($filename, true);
        }
    }

    function getSSLink($file)
    {
        $fileContent = base64_decode($file);
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
        if ("8382" == $port) {
            $port = "8387";

            $remark = iconv('GB2312', 'UTF-8', $this->ipl->getaddress($ip)['area1'] . $this->ipl->getaddress($ip)['area2']);
            $node = $is_SSR ? new SSR_Node($ip, $port,$remark) : new SS_Node($ip, $port);
            return $node->genLink("#$remark");
        }
        return false;

    }
}

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

    function genLink($remark = "")
    {

        return "ss://" . urlsafe_b64encode($this->link) . $remark;
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
    private $remark;//remark
    /**
     * SSR_Node constructor.
     * @param $host
     * @param $port
     * @param $method
     * @param $password
     * @param $protocol
     * @param $obfs
     */
    public function __construct($host, $port, $remark = "", $method = "aes-256-cfb", $password = "Sin1234qwer", $protocol = "origin", $obfs = "plain")
    {
        $this->host = $host;
        $this->port = $port;
        $this->method = $method;
        $this->password = $password;
        $this->protocol = $protocol;
        $this->obfs = $obfs;
        if ("" == $remark) {
            $this->$remark=$host;
        }
        $this->link = "$host:$port:$protocol:$method:$obfs:" . urlsafe_b64encode($password) . "/?group=" . urlsafe_b64encode("free") . "&remarks=" .
            urlsafe_b64encode($remark);
    }

    function genLink()
    {
        return "ssr://" . urlsafe_b64encode($this->link);
    }
}

function urlsafe_b64decode($string)
{
    $data = str_replace(array('-', '_'), array('+', '/'), $string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}


function urlsafe_b64encode($string)
{
    $data = base64_encode($string);
    $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
    return $data;
}

$getSS = new GetSS();
$is_dingyue = isset($_GET['dingyue']);
$update = isset($_GET['update']);
$filename = 'abc.txt';
$file = $getSS->getFileContent($filename, $update);
$link = $getSS->getSSLink($file);
$text = "";
foreach ($link as $item) {
    $node = $getSS->genNode($item, $is_dingyue);
    if ($node) $text .= $node . "\n";
}
if ($is_dingyue) {
    echo urlsafe_b64encode($text);
} else {
    echo str_replace("\n", "</br>", $text);
}
