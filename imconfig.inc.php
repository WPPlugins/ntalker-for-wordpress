<?php
define('KEY','domyself');

//网站在ntalker上注册的siteID(请修改为激活邮件中分配的siteid)
$im_siteid = "your siteID";

//注册Ntalker时分配到的sitekey，验证接口调用者是否为ntalker服务器，如没有从ntalker获得，请不要修改该选项
$sitekey = "your sitekey";

$siteurl = "your siteurl";

//ntalker插件功能开关
$im_enable = true;    //true:开启ntalker；false：关闭ntalker

//好友列表入口浮动开关（logo模式）
$im_float = true;     //true:启用浮动方式；false：启用固定方式

//是否使用ntalker sitekey验证
$enablesitekey = true;     //true:使用；false：不使用

//是否显示ntalker自定义的用户信息,true:显示；false：不显示
$isshowprofile = false;

//是否将用户使用ntalker获得的积分转换为网站积分,true:开启转换；false：关闭转换
$issyncmoney = false;

//用户使用ntalker获得的积分与网站积分的兑换率，也就是多少ntalker积分兑换网站的1个积分
$moneyrate = 1;

//是否向ntalker服务器推送网站最新动态，ture：推送；false：不推送
$im_enablefeedactivity = true;

//ntalker接收推送动态的服务器地址
$im_feedurl = "http://active.ntalker.com/reportactivity.php";

//是否装载自定义配置脚本，ture：装载；false：不装载
$im_enableLoadConfig = false;


//－－－－－－－－－－－－－－以下配置无需修改－－－－－－－－－－－－－－－－－
$im_wdkresource_server = "download.ntalker.com/res";
$im_imfunction_js = "http://".$im_wdkresource_server."/imfunction_utf8.js";

function im_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
	 $ckey_length = 6;
 	 $key = md5($key != '' ? $key : KEY);
	 $keya = md5(substr($key, 0, 16));
	 $keyb = md5(substr($key, 16, 16));
	 $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	 $cryptkey = $keya.md5($keya.$keyc);
	 $key_length = strlen($cryptkey);
	 $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	 $string_length = strlen($string);
     $result = '';
	 $box = range(0, 255);
	 $rndkey = array();
	 for($i = 0; $i <= 255; $i++) {
	 	 $rndkey[$i] = ord($cryptkey[$i % $key_length]);
	 }
	 for($j = $i = 0; $i < 256; $i++) {
		 $j = ($j + $box[$i] + $rndkey[$i]) % 256;
		 $tmp = $box[$i];
		 $box[$i] = $box[$j];
		 $box[$j] = $tmp;
	 }
	 for($a = $j = $i = 0; $i < $string_length; $i++) {
		 $a = ($a + 1) % 256;
		 $j = ($j + $box[$a]) % 256;
		 $tmp = $box[$a];
		 $box[$a] = $box[$j];
		 $box[$j] = $tmp;
		 $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	 }
	 if($operation == 'DECODE') {
		 if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			 return substr($result, 26);
		 } else {
			 return '';
		 }
	 } else {
		 return $keyc.str_replace('=', '', base64_encode($result));
	 }
}
?>
