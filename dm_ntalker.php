<?php
/*
Plugin Name: Ntalker for Wordpress
Plugin URI: http://www.domyself.me/lab/ntalker-for-wordpress
Description: This plugin can make you chat with others who are on your Wordpress site now by <a href="http://www.ntalker.com/" target="_blank">Ntalker</a>.
Version: 1.1
Author: ETY001
Author URI: http://www.domyself.me/about
*/

/*  Copyright 2011  ETY001  (email: ety001@domyself.me)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//引用imconfig.inc.php有关WDK的配置参数
require_once ('imconfig.inc.php');
//引用Cache_Lite缓存库
require_once ('Cache/Lite.php');
session_start ();
if(!class_exists('addJStoHeader')){
    class addJStoHeader {
		
		var $im_enable;
		var $im_siteid;
		var $im_imfunction_js;
		
		//construct
		function addJStoHeader($im_enable,$im_siteid,$im_imfunction_js) {
			global $user_id;
			$this->im_enable = $im_enable;
			$this->im_siteid = $im_siteid;
			$this->im_imfunction_js = $im_imfunction_js;
		}
		
        function add_JS_to_Header(){
			if(is_user_logged_in()) {
				global $userdata;
				get_currentuserinfo();
				$myuid = $userdata->ID;
				$myusername = $userdata->user_login;
				$mysessionid = URLencode(im_authcode($userdata->user_pass_md5."::".$userdata->ID,'CODE'));
			} else {			
				if (!$_SESSION ['username'] || strlen ( $_SESSION ['username'] ) <= 0) {
					$chose = '0123456789';
					$username = '0000';
					$sessionid = '0000';
					for($i = 0; $i < 4; $i ++) {
						$rand = rand ( 0, 9 );
						$username [$i] = $chose [$rand];
						$rand = rand ( 0, 9 );
						$sessionid [$i] = $chose [$rand];
					}
					$_SESSION ['uid'] = $username;
					$_SESSION ['username'] = 'TempUser'.$username;
					$_SESSION ['sessionid'] = 'sid'.$sessionid;
				}
			
				if (empty($_SESSION['username'])) {
					die ( "session 无效！" );
				}

				$myuid = $_SESSION ['uid'];
				$myusername = $_SESSION ['username'];
				$mysessionid = $_SESSION ['sessionid'];
				$now = time ();

				//设置缓存参数，缓存目录为当前目录下tmp，请保证tmp目录可读写，缓存时间设为1天
				$options = array (
					'automaticSerialization' => 'true', 
					'cacheDir' => '/tmp/', 
					'lifeTime' => 216000,
					'pearErrorMode' => CACHE_LITE_ERROR_DIE
				);
				$catchid = 'tempusers';
				$Cache_Lite = new Cache_Lite ( $options );

				if ($usersdata = $Cache_Lite->get( $catchid )) { //找到缓存对象，则从其中查找访问者数据
					if (isset ( $usersdata [$myuid] )) { //如果找到访问者ID对应的缓存数据，则更新访问时间参数
						$usersdata [$myuid] ["updatetime"] = $now;
					} else { //如果没有找到访问者ID对应的缓存数据，则生成该用户数据，放入缓存
						$usersdata [$myuid] = array ();
						$usersdata [$myuid] ["tempuser"] = 'true';
						$usersdata [$myuid] ["uid"] = $myuid;
						$usersdata [$myuid] ["username"] = $myusername;
						$usersdata [$myuid] ["createtime"] = $now;
						$usersdata [$myuid] ["updatetime"] = $now;
						$usersdata [$myuid] ["userip"] = $_SERVER ["REMOTE_ADDR"];
					}
				} else { //没有找到缓存数据，则生成缓存对象
					$usersdata = array ();
					$usersdata [$myuid] = array ();
					$usersdata [$myuid] ["tempuser"] = 'true';
					$usersdata [$myuid] ["uid"] = $myuid;
					$usersdata [$myuid] ["username"] = $myusername;
					$usersdata [$myuid] ["createtime"] = $now;
					$usersdata [$myuid] ["updatetime"] = $now;
					$usersdata [$myuid] ["userip"] = $_SERVER ["REMOTE_ADDR"];
				}
				$Cache_Lite->save($usersdata,$catchid);
			}
			
			if($this->im_enable){
				echo '<!--for dm_ntalker online chat begin Designed by ETY001-->';
				echo '<script type="text/javascript" src="' . $this->im_imfunction_js . '" charset="utf-8"></script>';
				echo '<script language="javascript" type="text/javascript">' . "im_connectIM('" . $this->im_siteid . "','" . $myuid . "','" . $myusername . "','" . $mysessionid . "', '');</script><!--for ntalker online chat end-->";
			}
        }
    }
}


if(class_exists('addJStoHeader')) {
    $dm_ntalker = new addJStoHeader($im_enable,$im_siteid,$im_imfunction_js);
}
if(isset($dm_ntalker)){
    add_action('wp_footer',array(&$dm_ntalker,'add_JS_to_Header'));
    add_action('admin_footer',array(&$dm_ntalker,'add_JS_to_Header'));
}


?>
