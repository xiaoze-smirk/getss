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
            $node = $is_SSR ? new SSR_Node($ip, $port, $remark) : new SS_Node($ip, $port, $remark);
            return $node;
        }
        return false;

    }
}


class SS_Node
{
    public $type = "ss";
    public $host;//服务器IP
    public $port;//端口
    public $method;//加密方法
    public $password;//密码
    public $link; //连接
    public $remark;//remark
    //pingms
    public $name;
    public $url;
    public $download;

    /**
     * SS_Node constructor.
     * @param $host
     * @param $port
     * @param $method
     * @param $password
     */
    public function __construct($host, $port, $remark = "", $method = "aes-256-cfb", $password = "Sin1234qwer")
    {
        $this->host = $host;
        $this->port = $port;
        $this->method = $method;
        $this->password = $password;
        $this->link = "$method:$password@$host:$port";
        $this->remark = $remark;
        $this->link = "ss://" . urlsafe_b64encode($this->link) . "#$remark";
        $this->name = $remark;
        $this->url = "http://$host/";
        $this->download=$this->link;
    }

}

class SSR_Node
{
    public $type = "ssr";
    public $host;//服务器IP
    public $port;//端口
    public $method;//加密方法
    public $password;//密码
    public $link; //连接
    public $protocol;//协议
    public $obfs;//混淆方式
    public $remark;//remark
    //ping ms
    public $name;
    public $url;
    public $download;
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
            $this->$remark = $host;
        } else {

            $this->remark = $remark;
        }
        $this->link = "$host:$port:$protocol:$method:$obfs:" . urlsafe_b64encode($password) . "/?group=" . urlsafe_b64encode("free") . "&remarks=" .
            urlsafe_b64encode($remark);

        $this->link = "ssr://" . urlsafe_b64encode($this->link);
        $this->name = $remark;
        $this->url = "http://$host/";
        $this->download=$this->link;
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
$result = array();
foreach ($link as $item) {
    $node = $getSS->genNode($item, $is_dingyue);
    if ($node) array_push($result, $node);
}

header('Content-Type: text/javascript; charset=utf-8');
echo "data={Free:" . json_encode($result, JSON_UNESCAPED_UNICODE) . "};";
