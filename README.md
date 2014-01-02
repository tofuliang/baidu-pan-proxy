
##用法
###普通代理
单文件:        index.php?/show|get/`shareid`/`uk[/文件名.扩展名]`         
文件夹:        index.php?/show|get/`shareid`/`uk`/`文件夹路径`/`文件索引[/文件名.扩展名]`         
文件夹遍历:  index.php?/folder/`shareid`/`uk`/`文件夹路径`/         
                    
#####show
show为显示下载链接(给下载工具调用)
#####get
get为直接重定向到文件(伪静态文件,可作音乐/视频在线播放用)         
#####folder
folder文件夹遍历是把该目录下所有下载链接揪出来的(给下载工具调用)         
#####中括号[]
`[]`内的为可选参数.可以不加                  
                    
###特殊代理
URL模式:      index.php?`baiduUrl`         
在`?`后直接加上共享文件的url地址,直接重定向到文件的真实地址.                 

指定文件名模式(限文件夹内文件):                    
index.php?/file/`shareid`/`uk`/`文件夹路径`/`文件名`                   
直接重定向到文件的真实地址.
                
输出图片可调质量图片模式(限文件内图片):                
index.php?/pic/`shareid`/`uk`/`文件夹路径`/c`输出图片宽度`u`输出图片高度`q`输出图片质量`/`文件名`                   

###使用例子:
####单文件
原链接为                    
`http://pan.baidu.com/share/link?shareid=322284&uk=1963222956`                    
获取真实地址为                    
`http://server/index.php?/show/322284/1963222956`                    
外链地址为                    
`http://server/index.php?/get/322284/1963222956`                    
或者可以添加任意文件名,如                    
`http://server/index.php?/get/322284/1963222956/1.mp3`                    

####文件夹
#####文件夹中单个文件
原链接为                    
`http://pan.baidu.com/share/link?shareid=166708&uk=419822042#dir/path=%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83`                    
获取真实地址为                    
`http://server/index.php?/get/166708/419822042/%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83/1`          (第1个文件填1,第二个填2,如此类推)                    
外链地址为                    
`http://server/index.php?/show/166708/419822042/%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83/1`          (第1个文件填1,第二个填2,如此类推)                    
或者可以添加任意文件名,如                    
`http://server/index.php?/get/166708/419822042/%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83/1/1.rar`    (第1个文件填1,第二个填2,如此类推)                    
                    
#####遍历文件夹并输出所有下载连接
原链接为                    
`http://pan.baidu.com/share/link?shareid=166708&uk=419822042#dir/path=%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83`                    
输出所有真实地址                    
`http://server/index.php?/folder/166708/419822042/%2F%E5%9B%BA%E4%BB%B6%2Ftomato%E7%BD%91%E9%80%9F%E4%BC%98%E5%8C%96%2F%E5%85%B6%E5%AE%83/`           (第1个文件填1,第二个填2,如此类推)                    
                    
####特殊代理
#####URL模式
原链接为                    
`http://pan.baidu.com/share/link?shareid=322284&uk=1963222956`
重定向到真实地址                    
`http://server/index.php?http://pan.baidu.com/share/link?shareid=322284&uk=1963222956`                             
                    
#####指定文件名模式
分享文件夹链接为                    
`http://pan.baidu.com/share/link?shareid=384500&uk=1963222956#dir/path=%2Fblog_pic`                    
文件名为                    
`P1050091.JPG`                   
重定向到真实地址                  
`http://server/index.php?/file/384500/1963222956/%2Fblog_pic/P1050091.JPG`                  
                 
#####输出图片可调质量图片模式
分享文件夹链接为                    
`http://pan.baidu.com/share/link?shareid=384500&uk=1963222956#dir/path=%2Fblog_pic`                    
文件名为                    
`P1050091.JPG`                   
输出限定高为450px,宽为500px,质量为90%的缩略图地址为(按比例根据指定的较短的边缩放)                    
`http://server/index.php?/pic/384500/1963222956/%2Fblog_pic/c450u500q90/P1050091.JPG`                  

##附录
把下面的server/index.php修改成相应地址后，添加到收藏夹可以更方便的使用（不支持文件夹）
```js
javascript: void((function() { var tmpurl, srcurl = location.href; tmpurl = srcurl.replace('pan.baidu.com/share', 'server/index.php').replace('/link?', '/?/get').replace('shareid=', '\/').replace('&uk=', '\/').replace('#dir/path=', '\/'); var name=prompt('请右键复制URL，或者点击确定直接下载',tmpurl); if(name!=null && name!='') window.open(tmpurl,'');})())
```
#####更新
V0.91                                
当获取下载链接需要验证码时,判断该文件是否有缩略图,有则输出缩略图地址,否则不输出任何东西.

V0.9                               
跟进百度网盘改版,特殊代理模式支持短链接
百度已经会用验证码限制,能否获取真实地址看RP
不建议遍历输出文件夹所有链接,成功率极低,同时可能导致该IP长时间需要验证码.
作图床使用的,建议使用`输出图片可调质量图片模式`,因为百度需要输出预览图,以后改版,这处改动应该不大.不过图片质量稍有损失.