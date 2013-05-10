<?php
// STARTOFFILE

// URL的正则
$url = '/http:\/\/pan\.baidu\.com.*(?:shareid=(\d+)&(?:uk=(\d+?)))/Uis';

// 文件夹的正则
$folder = '/\/(get|show|folder)\/(\d+)\/(\d+)\/(.+)(?:\/(\d+)[\/]((?:.+)\.(?:.+?))|\/(\d+?)[\/]{0,1}|\/)/Uis';

// 单文件的正则
$file = '/\/(get|show)\/(\d+)\/(\d+?)(?:[\/]((?:.+)\.(?:.+?))|[\/]{0,1})/Uis';

//文件夹指定文件名的正则
$fileName = '/\/(file)\/(\d+)\/(\d+)\/(.+)\/(.+?)/Uis';

//文件夹指定文件名的正则(扩展:为图片是增加尺寸,质量等参数)
$picName = '/\/pic\/(\d+)(?#shareid)\/(\d+)(?#uk)\/(.+)(?#path)\/(c\d+?)(u\d+?)q(\d+?)(?#图片参数)\/(.+?)(?#文件名)/Uis';

$preg=array('url'=>$url,'folder'=>$folder,'file'=>$file,'picName'=>$picName,'fileName'=>$fileName);

class BaiduPanProxy{
    private $uk;
    private $shareid;
    private $path; //文件夹路径
    private $file; //指定输出为图片时的文件名
    private $size; //指定输出为图片时的尺寸
    private $method; //输出方法
    private $folderIndex; //文件夹的文件索引号
    private $folder;//文件夹的json内容
    private $url='';
    private $preg=array();
    // public $trueLinkPreg='|(http:[\\\/]{2,6}www\.baidupcs\.com[\\\/]{1,3}file[\\\/]{1,3}.*)\\\"|U'; //已失效
    public $trueLinkPreg='|(http:[\\\/]{2,6}d\.pcs\.baidu\.com[\\\/]{1,3}file[\\\/]{1,3}.*)\\\"|U';

    public function __construct($url,$preg) {
        $this->url = $url;
        $this->preg = $preg;
    }
    private function parseUrl(){
        foreach ($this->preg  as $k=>$value) {
            if(0 != preg_match ( $value, $this->url, $match )){
                switch ($k) {
                    case 'url':
                        list ( , $this->shareid, $this->uk) = $match;
                        $this->method='get';//此情况下默认是get,不能自定义
                        $this->matchBy='url';
                        break;

                    case 'folder':
                        if(8 == count ( $match )) // 文件夹,不带文件名
                            list ( , $this->method, $this->shareid, $this->uk, $this->path,,,$this->folderIndex) = $match;
                        if(5 == count ( $match )) // 文件夹,遍历
                            list ( , $this->method, $this->shareid, $this->uk, $this->path) = $match;
                        if(7 == count ( $match )) // 文件夹,带文件名
                            list ( , $this->method, $this->shareid, $this->uk, $this->path,$this->folderIndex) = $match;
                        $this->folderIndex=$this->folderIndex-1;
                        $this->matchBy='folder';
                        // var_dump($match);exit();
                        break;

                    case 'file':
                        list ( , $this->method,$this->shareid, $this->uk) = $match;
                        $this->matchBy='file';
                        break;

                    case 'picName':
                        list ( , $this->shareid, $this->uk, $this->path, $c,$u,$q,$this->file) = $match;
                        $this->size='size='.$c.'_'.$u.'&quality='.$q;
                        $this->file=urldecode($this->file);
                        $this->matchBy='picName';
                        break;

                    case 'fileName':
                        list ( , ,$this->shareid, $this->uk, $this->path,$this->file) = $match;
                        $this->file=urldecode($this->file);
                        $this->matchBy='fileName';
                        break;
                }
                return $this;
            }
        }
        if(!$match) $this->error();//匹配不到任何正确的参数则退出
    }
    private function parseFolder($url){
        $html=file_get_contents($url);
        return json_decode($html,true);
    }
    private function searchFolder(){
        $url = "http://pan.baidu.com/share/list?dir=" . $this->path . "&shareid=" . $this->shareid . "&uk=" . $this->uk . "&";
        $this->json=$this->parseFolder($url);
        if(! is_array($this->json)) $this->error();
    }
    private function getFolderIndex($list){
        foreach ($list as $k=>$v) {
            if(strtolower($v['server_filename'])==strtolower($this->file) )return $k;
        }
    }
    private function digFolder($path = '', $shareid = '', $uk = ''){
        $url = "http://pan.baidu.com/share/list?dir=" . $path . "&shareid=" . $shareid . "&uk=" . $uk . "&";
        $this->json=$this->parseFolder($url);
        if(! is_array($this->json)) $this->error();
        foreach ( $this->json ['list'] as $arr ) {
            if (1 == $arr ['isdir'])
                $this->digFolder ( urlencode ( $arr ['path'] ), $shareid, $uk );
            else
                $this->str .= "<a href=\"" . $arr ['dlink'] . "\">" . $arr ['server_filename'] . "</a><br />";
        }
        return $this->str;
    }
    private function getRealLink(){
        if(!$this->method){//如果没有方法,只有输入真实文件名或输出可调质量图片两种方法
            $this->searchFolder();
            $this->folderIndex=$this->getFolderIndex($this->json['list']);
            if($this->size){
                //调节图片输出质量
                $this->realLink=preg_replace("/size=c\d+_u\d+&quality=\d+/", $this->size, $this->json['list'][$this->folderIndex]['thumbs']['url3']);
            }else{
                $this->realLink=$this->json['list'][$this->folderIndex]['dlink'];
            }
        }elseif ('folder' == $this->method) {
            $this->realLink = $this->digFolder($this->path,$this->shareid ,$this->uk);
        }else{
            if(!$this->path){
                $url = "http://pan.baidu.com/share/link?shareid=" . $this->shareid . "&uk=" . $this->uk;
                $html = file_get_contents ( $url );
                if (0 == preg_match ( $this->trueLinkPreg, $html, $url ))
                    $this->error();
                $_string = array ("replace" => Array ("&amp;", '\\' ), "string" => Array ("&", "" ) );
                // 替换转义字符
                $this->realLink = str_ireplace ( $_string ["replace"], $_string ["string"], $url [1] );
            }elseif ($this->path) {
                $this->searchFolder();
                $this->realLink=$this->json['list'][$this->folderIndex]['dlink'];
            }
        }
    }
    public function haveFun(){
        if ($this->checkNew ( $this->localVersion, $this->checkFrequency ))
            $this->updateFile ( $this->localVersion );
        $this->parseUrl()->getRealLink();
        if('show'==$this->method || 'folder'==$this->method)
            echo $this->realLink;
        else
            header ( "Location:" . $this->realLink );
    }
    private function error(){
        die('oops!! 获取失败诶~建议到<a href="https://github.com/tofuliang/baidu-pan-proxy" target="_blank">https://github.com/tofuliang/baidu-pan-proxy</a>了解了解吧~');
    }
    private function checkNew($localVersion, $checkFrequency) {
        $lastCheck = ( int ) @file_get_contents ( __DIR__ . "/.lastcheck" );
        if (time () - $lastCheck > $checkFrequency) {
            @file_put_contents ( __DIR__ . "/.lastcheck", time () );
            $serverVersion = ( double ) @file_get_contents ( "https://raw.github.com/tofuliang/baidu-pan-proxy/master/version" );
            if ($serverVersion - ( double ) $localVersion > 0)
                return true;
            return false;
        }
        return false;
    }
    public function updateFile($localVersion) {
        $new = ( string ) @file_get_contents ( "https://raw.github.com/tofuliang/baidu-pan-proxy/master/index.php" );
        if (strpos ( $new, 'STARTOFFILE' ) && strpos ( $new, 'ENDOFFILE' )) {
            copy ( __FILE__, __FILE__ . $localVersion ) && @file_put_contents ( __FILE__, $new ) && exit ( "代理程序已更新到新版" );
        }
    }


}
$link = new BaiduPanProxy($_SERVER ["QUERY_STRING"],$preg);
$link->localVersion = '0.6';
$link->checkFrequency = 86400; // 每隔多少秒到服务器检测更新,默认是一天
$link->haveFun();
//ENDOFFILE