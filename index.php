<?php
// STARTOFFILE

/**
*
* @author tofuliang <tofuliang@gmail.com>
*/

$useage = '
<pre>
参数错误!!!(如果确认参数没有错,请检查程序是否有新版本)

使用方法:
单文件:     index.php?/show|get/shareid/uk[/文件名.扩展名]
文件夹:     index.php?/show|get/shareid/uk/文件夹路径/文件索引[/文件名.扩展名]
文件夹遍历: index.php?/folder/shareid/uk/文件夹路径/

show为显示下载链接(给下载工具调用),get为直接重定向到文件(伪静态文件,可作音乐/视频在线播放用)
文件夹遍历是把该目录下所有下载链接揪出来的(给下载工具调用)
中括号内的为可选参数
使用例子:


单文件:
-------------------------------========================================++++++++++++++++++++++++++++++++++++++++++++++++++========================================-------------------------------

原链接为
http://pan.baidu.com/share/link?shareid=322284&uk=1963222956
获取真实地址为
http://server/index.php?/show/322284/1963222956
外链地址为
http://server/index.php?/get/322284/1963222956
或者可以添加任意文件名,如
http://server/index.php?/get/322284/1963222956/1.mp3

0.4版新增单文件URL作为参数
举例
原链接为
http://pan.baidu.com/share/link?shareid=322284&uk=1963222956
获取真实地址为
http://server/index.php?http://pan.baidu.com/share/link?shareid=322284&uk=1963222956

-------------------------------========================================++++++++++++++++++++++++++++++++++++++++++++++++++========================================-------------------------------

文件夹:
-------------------------------========================================++++++++++++++++++++++++++++++++++++++++++++++++++========================================-------------------------------

原链接为
http://pan.baidu.com/share/link?shareid=166708&uk=419822042#dir/path=%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83
获取真实地址为
http://server/index.php?/show/166708/419822042/%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83/1          (第1个文件填1,第二个填2,如此类推)
外链地址为
http://server/index.php?/show/166708/419822042/%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83/1          (第1个文件填1,第二个填2,如此类推)
或者可以添加任意文件名,如
http://server/index.php?/show/166708/419822042/%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83/1/1.rar    (第1个文件填1,第二个填2,如此类推)

-------------------------------========================================++++++++++++++++++++++++++++++++++++++++++++++++++========================================-------------------------------

文件夹遍历:
-------------------------------========================================++++++++++++++++++++++++++++++++++++++++++++++++++========================================-------------------------------

原链接为
http://pan.baidu.com/share/link?shareid=166708&uk=419822042#dir/path=%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83
获取真实地址为
http://server/index.php?/folder/166708/419822042/%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83/           (第1个文件填1,第二个填2,如此类推)

-------------------------------========================================++++++++++++++++++++++++++++++++++++++++++++++++++========================================-------------------------------

</pre>项目主页 <a href="https://github.com/tofuliang/baidu-pan-proxy">https://github.com/tofuliang/baidu-pan-proxy</a>
'."<br /><br /><br />把下面的server/index.php修改成相应地址后，添加到收藏夹可以更方便的使用（不支持文件夹）<pre>javascript: void((function() { var tmpurl, srcurl = location.href; tmpurl = srcurl.replace('pan.baidu.com/share', 'server/index.php').replace('/link?', '/?/get').replace('shareid=', '\/').replace('&uk=', '\/').replace('#dir/path=', '\/'); var name=prompt('请右键复制URL，或者点击确定直接下载',tmpurl); if(name!=null && name!='') window.open(tmpurl,'');})())</pre>
";
$localVersion = '0.4';
$checkFrequency = 86400; // 每隔多少秒到服务器检测更新,默认是一天

if (checkNew ( $localVersion, $checkFrequency ))
    updateFile ( $localVersion );

// URL的正则
$reg_url = '/http:\/\/pan\.baidu\.com.*(?:shareid=(\d+)&(?:uk=(\d+?)))/Uis';

// 文件夹的正则
$reg_folder = '/\/(get|show|folder)\/(\d+)\/(\d+)\/(.+)(?:\/(\d+)[\/]((?:.+)\.(?:.+?))|\/(\d+?)[\/]{0,1}|\/)/Uis';

// 单文件的正则
$reg_file = '/\/(get|show)\/(\d+)\/(\d+?)(?:[\/]((?:.+)\.(?:.+?))|[\/]{0,1})/Uis';

if (0 == preg_match ( $reg_url, $_SERVER ["QUERY_STRING"], $match ))
    if (0 == preg_match ( $reg_folder, $_SERVER ["QUERY_STRING"], $match )) {
        if (0 == preg_match ( $reg_file, $_SERVER ["QUERY_STRING"], $match ))
            die ( $useage );
// 如果三个表达式都不匹配,说明参数有误,退出
    }

// 遍历文件夹列出所有文件
    if (5 == count ( $match ) && 'folder' == $match [1])
        list ( , $method, $shareid, $uk, $path ) = $match;

// 单文件
    if (4 <= count ( $match ) &&  'folder' != $match [1])
        list ( , $method, $shareid, $uk, $filename ) = $match;

// 文件夹,不带文件名
    if (8 == count ( $match ) && 'folder' != $match [1])
        list ( , $method, $shareid, $uk, $path, , , $index ) = $match;

// 文件夹,带文件名
    if (7 == count ( $match ) && 'folder' != $match [1])
        list ( , $method, $shareid, $uk, $path, $index, $filename ) = $match;

    if (3 == count ( $match )) {
        list ( ,$shareid, $uk ) = $match;
        $method = 'get';
    }

// 最重要两个参数木有就退出
    if (! $shareid || ! $uk)
        die ( $useage );

// 递归挖掘目录函数
    function dig($path = '', $shareid = '', $uk = '') {
        $url = "http://pan.baidu.com/share/list?dir=" . $path . "&shareid=" . $shareid . "&uk=" . $uk . "&";
        $url = file_get_contents ( $url );
        $url = json_decode ( $url, true );
        if (! is_array ( $url ))
            die ( $useage );
        foreach ( $url ['list'] as $arr ) {
// var_dump($arr);
            if (1 == $arr ['isdir'])
                dig ( urlencode ( $arr ['path'] ), $shareid, $uk );
            else
                echo ("Download link:<a href=\"" . $arr ['dlink'] . "\">" . $arr ['server_filename'] . "</a><br />");
        }
    }
    if ('folder' == $method) {
// 输出文件夹链接
        dig ( $path, $shareid, $uk );
    } elseif ('show' == $method) {
// 输出下载链接
        if ($path) {
            $url = "http://pan.baidu.com/share/list?dir=" . $path . "&shareid=" . $shareid . "&uk=" . $uk . "&";
            $url = file_get_contents ( $url );
            $url = json_decode ( $url, true );
            if (! is_array ( $url ))
                die ( $useage );
// var_dump($url);
            $url = $url ['list'] [$index - 1] ['dlink'];
            if ($url)
                exit ( "Download link:<a href=\"" . $url . "\">" . $url . "</a>" );
            exit ( $useage );
        } else {
            $url = "http://pan.baidu.com/share/link?shareid=" . $shareid . "&uk=" . $uk;
            $url = file_get_contents ( $url );
            if (0 == preg_match ( '|(http:[\\\/]{2,6}www\.baidupcs\.com[\\\/]{1,3}file[\\\/]{1,3}.*)\\\"|U', $url, $url ))
                exit ( $useage );
            $_string = array ("replace" => Array ("&amp;", '\\' ), "string" => Array ("&", "" ) );
// 替换转义字符
            $url = str_ireplace ( $_string ["replace"], $_string ["string"], $url [1] );
            exit ( "Download link:<a href=\"" . $url . "\">" . $url . "</a>" );
        }
    } elseif ('get' == $method) {
// 输出重定向
        if ($path) {
            $url = "http://pan.baidu.com/share/list?dir=" . $path . "&shareid=" . $shareid . "&uk=" . $uk . "&";
            $url = file_get_contents ( $url );
            $url = json_decode ( $url, true );
// print_r($url);
            if (! is_array ( $url ))
                die ( $useage );
            $url = $url ['list'] [$index - 1] ['dlink'];
            header ( "Location:" . $url );
        } else {
            $url = "http://pan.baidu.com/share/link?shareid=" . $shareid . "&uk=" . $uk;
            $url = file_get_contents ( $url );
            if (0 == preg_match ( '|(http:[\\\/]{2,6}d\.pcs\.baidu\.com[\\\/]{1,3}file[\\\/]{1,3}.*)\\\"|U', $url, $url ))
                exit ( $useage );
            $_string = array ("replace" => Array ("&amp;", '\\' ), "string" => Array ("&", "" ) );
// 替换转义字符
            $url = str_ireplace ( $_string ["replace"], $_string ["string"], $url [1] );
            header ( "Location:" . $url );
        }
    }
    function checkNew($localVersion, $checkFrequency) {
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
    function updateFile($localVersion) {
        $new = ( string ) @file_get_contents ( "https://raw.github.com/tofuliang/baidu-pan-proxy/master/index.php" );
        if (strpos ( $new, 'STARTOFFILE' ) && strpos ( $new, 'ENDOFFILE' )) {
            copy ( __FILE__, __FILE__ . $localVersion ) && @file_put_contents ( __FILE__, $new ) && exit ( "代理程序已更新到新版" );
        }
    }
//ENDOFFILE