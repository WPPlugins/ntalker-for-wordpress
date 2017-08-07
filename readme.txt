=== Ntalker for Wordpress ===
Contributors: ETY001
Donate link: http://www.domyself.me/
Tags: chat,online
Requires at least: 3.0.0
Tested up to: 3.2.1
Stable tag: 1.1

This plugin can make you chat with others who are on your Wordpress site now by Ntalker.

== Description ==

This plugin can make you chat with others who are on your Wordpress site now by <a href="http://www.ntalker.com">Ntalker</a>.<br />
这个插件可以使你利用<a href="http://www.ntalker.com">Ntalker</a>来与当前访问你的Wordpress的浏览者进行在线即时聊天。

== Installation ==

1. 首先你需要去<a href="http://www.ntalker.com">Ntalker</a>的官方网站注册一个用户并开通一个站点。在注册站点的时候，需要你选择其他网站程序，另外接口地址处填写http://yourdomain/wp-content/plugins/dm_ntalker/imxml.php（比如我的网站地址是：<a href="http://www.domyself.me">www.domyself.me</a>，那么我的接口地址就是http://www.domyself.me/wp-content/plugins/ntalker-for-wordpress/imxml.php）。
2. 注册成功后，通过Wordpress面板安装给插件，并且通过ftp把改插件目录下的crossdomain.xml复制到Wordpress的根目录下。
3. 从ntalker官网你注册的那个站点的控制面板下载SDK包，解压缩这个包，其中含有siteKey等重要信息，把这些信息，自己手动修改进该插件目录下的imconfig.inc.php文件的指定位置。
4. 最后，你可以向ntalker的客服提交开通申请了。

== Frequently Asked Questions ==
如果有问题，请访问这里进行留言：<a href="http://www.domyself.me/lab/ntalker-for-wordpress">http://www.domyself.me/lab/ntalker-for-wordpress</a>
= A question that someone might have =
如果有问题，请访问这里进行留言：<a href="http://www.domyself.me/lab/ntalker-for-wordpress">http://www.domyself.me/lab/ntalker-for-wordpress</a>

== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 1.1 =
* 调整了配置文件里的配置选项的位置，方便手动修改。预计在1.2版本中去掉该文件，实现在wp后台进行相关数据的配置。
* 修复了登录用户的用户名显示问题。
* 修复了登录用户无法进行群聊的问题。
* 对于已有接口文件代码进行了部分优化。

== Upgrade Notice ==
= 1.1 =
是1.0版本的改进版，解决的了1.0版本的已知问题。
