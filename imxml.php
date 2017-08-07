<?php
/* 
 * Plug Name: Ntalker for Wordpress
 * Plug Author: ETY001 
 * Blog: www.domyself.me 
 */
$dm_ntalker_version = '3.0.0';
$dm_ntalker_build = '20110722';
require_once('../../../wp-config.php');
require_once('./imconfig.inc.php');

//接口文件优化系数 high：性能优先；low：扩展性优先
$imxmlperf = "low";

header ( "Content-Type: text/xml; charset=utf-8" );
echo ("<?xml version='1.0' encoding='utf-8'?>");
echo ("<imxml encoding='utf-8'>");

$querytype = isset ( $_GET ['query'] ) ? $_GET ['query'] : null;
$querysid = isset ( $_GET ['sid'] ) ? $_GET ['sid'] : null;
$isuserprofile = isset ( $_GET ['isuserprofile'] ) ? $_GET ['isuserprofile'] : null;
$isdetail = isset ( $_GET ['isdetail'] ) ? $_GET ['isdetail'] : null;
$queryusername = isset ( $_GET ['username'] ) ? $_GET ['username'] : null;
$queryuserid = isset ( $_GET ['uid'] ) ? $_GET ['uid'] : null;
$querysrcuid = isset ( $_GET ['srcuid'] ) ? $_GET ['srcuid'] : null;
$newbuddyname = isset ( $_GET ['newbuddyname'] ) ? $_GET ['newbuddyname'] : null;
$newbuddyid = isset ( $_GET ['newbuddyid'] ) ? $_GET ['newbuddyid'] : null;
$delbuddyid = isset ( $_GET ['delbuddyid'] ) ? $_GET ['delbuddyid'] : null;
$destid = isset ( $_GET ['destid'] ) ? $_GET ['destid'] : null;
$userkey = isset($_GET['userkey']) ? $_GET['userkey'] : null; 
$pagesize = isset($_GET['pagesize']) ? $_GET['pagesize'] : null; 
$pageindex = isset($_GET['pageindex']) ? $_GET['pageindex'] : null;
$fuids = isset($_GET['fuids']) ? $_GET['fuids'] : null;
$isfuidlist = isset($_GET['isfuidlist']) ? $_GET['isfuidlist'] : null;
$istempuser = false;
$tempusername = '';

if (!$querytype)
{
	  echo ("<error>no valid query param</error>");
	  echo ("</imxml>");
	  return;
}

//引用Cache_Lite缓存库
require_once ('Cache/Lite.php');
$options = array (
	'automaticSerialization' => 'true', 
	'cacheDir' => '/tmp/', 
	'lifeTime' => 216000,
	'pearErrorMode' => CACHE_LITE_ERROR_DIE
);
$catchid = 'tempusers';
$Cache_Lite = new Cache_Lite ( $options );
if ($usersdata = $Cache_Lite->get( $catchid )) { //找到缓存对象，则从其中查找访问者数据
	if (isset($usersdata [$queryuserid])) { //如果找到访问者ID对应的缓存数据，则更新访问时间参数
		$usersdata [$queryuserid] ["updatetime"] = $now;
		$tempusername = $usersdata [$queryuserid] ["username"];
		$istempuser = $usersdata [$queryuserid] ["tempuser"];
	}
}

//up here is OK!!!

switch ($querytype)
{
	case "imxmlversion":
		  getImxmlVersion();
		  break;
	case "login" :
		  getLogin ($querysid, $queryuserid, $isuserprofile, $isdetail);
	 	  break;
	case "userprofile" :
		  getUserProfile($querysid, $queryuserid, $userkey, $destid, $isdetail);
		  break;
	case "siteprofile" :
		  getSiteProfile();
		  break;
	case "buddylist" :
		  getBuddyList($querysid, $queryuserid, $userkey, $pagesize, $pageindex, $isfuidlist, $isdetail, $fuids);
		  break;
	case "addbuddy":
		  getAddBuddy($querysid, $queryuserid, $userkey, $newbuddyid);
		  break;
	case "delbuddy":
		  getDelBuddy ($querysid, $queryuserid, $userkey, $delbuddyid);
		  break;
	default :
		  echo ("<error>query param is not valide</error>");
}

echo ("</imxml>");

//1-返回IMXML版本号
function getImxmlVersion()//OK!!!
{
	 global $dm_ntalker_build;
	 global $dm_ntalker_version;
 	 echo "<version>".$dm_ntalker_version."</version>";
     echo "<for>custom</for>";
     echo "<build>".$dm_ntalker_build."</build>";
     return;
}
//2-验证用户是否在网站登录成功
function  getLogin($querysid, $queryuserid, $isuserprofile=false, $isdetail=false)
{
	global $wpdb,$dm_ntalker_version,$queryusername,$istempuser,$istempuser,$tempusername;//OK!!!
	echo '<version>'.$dm_ntalker_version.'</version>';//OK!!!
	
	//anonymous  //OK!!!
	if($istempuser) {
		echo '<sessionvalide>true</sessionvalide>';
		$isuserprofile=false;
		$isdetail=false;
		echo '<userprofile>';
		echo '<uid>'.$queryuserid.'</uid>';
		echo '<name>'.im_xmlsafestr(htmlspecialchars($tempusername)).'</name>';
		echo '</userprofile>';
		return true;		
	} 
	
	if(!$queryuserid || !$querysid) {
		echo '<sessionvalide>false</sessionvalide>';
		return false;
	}
	//@list($password, $uid) = explode("::",im_authcode($querysid, 'DECODE'));//$querysid中存储着已登录用户的uid
	$queryuserid = intval($queryuserid);
	$uid = $queryuserid;
	//判断提交的$queryuserid和$querysid中存储着已登录用户的uid是否相同，相同则说明已等陆
	if($queryuserid == $uid) {
		echo '<sessionvalide>true</sessionvalide>';
		if($isuserprofile) {
			echo '<userprofile>';
			echo '<uid>'.$queryuserid.'</uid>';
			echo '<name>'.im_xmlsafestr(htmlspecialchars(get_usermeta($uid,'nickname'))).'</name>';
			echo '<isdefaulticon>1</isdefaulticon>';
			echo '<sex>-1</sex>';
			if($isdetail) {
				/*echo "<nick>".im_xmlsafestr(htmlspecialchars(get_usermeta($uid,'nickname')))."</nick>";
				echo "<email>".get_usermeta($uid,'user_email')."</email>";     
				echo "<credit>0</credit>";
				echo "<money>0</money>";
				echo "<profileinfo>".get_usermeta($uid,'description')."</profileinfo>";*/
			}
			echo "</userprofile>";
		}
		return true;
	} else {
		echo "<sessionvalide>false</sessionvalide>";
		return false;
	}
}
//3-获取网站相关WDK配置//OK!!!
function getSiteProfile(){
    global $wpdb,$enablesitekey,$dm_ntalker_version,$dm_ntalker_build;
    $enablesitekey = $enablesitekey ? "true" : "false";
    $systimestamp = time();
    $bloginfo = $wpdb->get_row("SELECT option_value FROM $wpdb->options where option_name = 'blogname'" );
    echo "<software>custom</software>";
    echo "<softwareversion>3.0.0</softwareversion>";
    echo "<language>utf-8</language>";
	echo "<isusesitekey>".$enablesitekey."</isusesitekey>";
	echo "<systimestamp>".$systimestamp."</systimestamp>";
	echo "<sitenanme>".$bloginfo->option_value."</sitenanme>";
	echo "<version>".$dm_ntalker_version."</version>";
	echo "<build>".$dm_ntalker_build."</build>";
	echo "<groupchatmsgtothread>false</groupchatmsgtothread>";
    echo "<mycenter>false</mycenter>";
    echo "<sitefocus>false</sitefocus>";
    echo "<sitehavegroup>false</sitehavegroup>";
}

//4-获取用户个人信息，用于显示在聊天窗口中
function getUserProfile($querysid, $queryuserid, $userkey, $destid, $isdetail)
{
	global $queryusername,$dm_ntalker_version,$tempusername,$istempuser,$usersdata;
	if($istempuser) {
		if(!sitekey_login($queryuserid, $userkey))
		{
			return;
		}
		if(!$destid)
		{
			echo "<error>query destid param not valid</error>";
			return;
		}
		$queryuserid = intval($queryuserid);
		$destid = intval($destid);
		$isdetail = $isdetail ? ($isdetail=="true" ? 1 : 0): 0;
		echo '<userprofile>';
		echo '<uid>'.$destid.'</uid>';
		if($tempname=$usersdata [$destid] ["username"]) {
			echo '<name>'.im_xmlsafestr(htmlspecialchars($tempname)).'</name>';
		} else {
			echo '<name>'.im_xmlsafestr(htmlspecialchars(get_usermeta($destid,'nickname'))).'</name>';
		}
		echo '</userprofile>';
		return true;
	}
	
	if(!sitekey_login($queryuserid, $userkey))
	{
		return;
	}
	if(!$destid)
	{
		echo "<error>query destid param not valid</error>";
		return;
	}
	$queryuserid = intval($queryuserid);
	$destid = intval($destid);
	$isdetail = $isdetail ? ($isdetail=="true" ? 1 : 0): 0;
	
	echo "<userprofile>";
	echo "<uid>".$destid."</uid>";
	echo "<name>".im_xmlsafestr(htmlspecialchars(get_usermeta($destid,'nickname')))."</name>";
	//echo "<icon><![CDATA[http://www.domyself.me]]></icon>";
	echo "<isdefaulticon>-1</isdefaulticon>";
	//echo "<profileurl><![CDATA[http://www.domyself.me]]></profileurl>";
	//echo "<usergroup id=\"".get_usermeta($destid,'wp_user_level')."\">-1</usergroup>"; 
	echo "<sex>-1</sex>"; 

	if($isdetail)
	{
		//echo "<nick>".im_xmlsafestr(htmlspecialchars(get_usermeta($destid,'nickname')))."</nick>";
		//echo "<email>".get_usermeta($destid,'user_email')."</email>";     
		//echo "<credit>0</credit>";
		//echo "<money>0</money>";
		echo "<profileinfo>".get_usermeta($destid,'description')."</profileinfo>";
	}
	echo "</userprofile>";
}
//5-获取用户好友列表
function getBuddyList($querysid, $queryuserid, $userkey, $pagesize=255, $pageindex=0, $isfuidlist, $isdetail, $fuids)
{
/*
	global $config, $im_version,$DatabaseHandler,$imxmlperf,$SITEURI;
	$_fuid='';
	if(!sitekey_login($queryuserid, $userkey))
	{
		return;
	}
	$queryuserid = intval($queryuserid);
	$pagesize =  intval($pagesize) > 0 ? intval($pagesize) : 255;
	$pageindex = $pageindex ? intval($pageindex) : 0;     
	$isdetail = $isdetail ? ($isdetail=="true" ? 1 : 0): 0;
	$isfuidlist = $isfuidlist ? (strtolower($isfuidlist)=='true' ? 1 : 0): 0;
	$sql="select uid,username from ".$config['db_table_prefix']."members where uid in(select uid from ".$config['db_table_prefix']."buddys where buddyid='$queryuserid' and uid in(select buddyid from ".$config['db_table_prefix']."buddys where uid='$queryuserid'))";
	$query_num = $DatabaseHandler->Query($sql);
	$buddynum = $query_num->GetNumRows();
	echo "<pageindex>".$pageindex."</pageindex>";
	echo "<pagesize>".$pagesize."</pagesize>";
	if($fuids && $buddynum)
	{
		$fuids = uid_safecheck($fuids,$buddynum);//fuids安全检查
	}
	if($isfuidlist || !$fuids)
	{
		echo '<allbuddynum>'.$buddynum.'</allbuddynum>';
	}
	if($isfuidlist)
	{
		echo '<buddyuids>';
	}
	else
	{
		echo '<buddylist>';
	}
	
	
	if($buddynum)
	{
		$start =  page_start($pageindex, $pagesize, $buddynum);
		$addsql = $addcoum = $groups = '';
		if($pagesize<$buddynum)
		{
			$addsql = 'ORDER BY uid asc';
		}
		if($isfuidlist)
		{
			$querysql = "select uid from ".$config['db_table_prefix']."buddys where buddyid='".$queryuserid."' and uid in(select buddyid from ".$config['db_table_prefix']."buddys where uid='".$queryuserid."') ".$addsql;
		}
		else
		{
			if($isdetail)
			{
				$addcoum = ',s.email,s.extcredits2,s.credits';
			}
			if($fuids)
			{
				$querysql = "select s.uid,s.username,s.nickname,s.ucuid,t.name,s.gender,s.bday,s.aboutme,s.province,s.city,s.role_id".$addcoum." from ".$config['db_table_prefix']."members s,".$config['db_table_prefix']."role t where s.role_id = t.id AND s.uid = ".$fuids." AND s.uid in(select uid from ".$config['db_table_prefix']."buddys where buddyid='".$queryuserid."' and uid in(select buddyid from ".$config['db_table_prefix']."buddys where uid='".$queryuserid."')) ".$addsql;
			}
			else
			{
				$querysql = "select s.uid,s.username,s.nickname,s.ucuid,t.name,s.gender,s.bday,s.aboutme,s.province,s.city,s.role_id".$addcoum." from ".$config['db_table_prefix']."members s,".$config['db_table_prefix']."role t where s.role_id = t.id AND s.uid in(select uid from ".$config['db_table_prefix']."buddys where buddyid='".$queryuserid."' and uid in(select buddyid from ".$config['db_table_prefix']."buddys where uid='".$queryuserid."')) ".$addsql." LIMIT ".$start.",".$pagesize;
			}
		} 
        $query = $DatabaseHandler->Query($querysql);
		while($row = $query->GetRow())
		{
			if($isfuidlist)
			{
				$_fuid = $_fuid ? $_fuid.','.$row['uid'] : $row['uid'];
			}
			else
			{
				$usericon = im_avatar($row['ucuid']);
				$genders = $row['gender']==0 ? -1 : ($row['gender']==1 ? "male" : "female");
				$bday = $row['bday'];
				$bday = ($row['bday']=='0000-00-00') ? "" : $bday;
				echo "<buddy>";
					echo "<uid>".$row['uid']."</uid>";
					echo "<name>".im_xmlsafestr(htmlspecialchars($row["nickname"]))."</name>";
					echo "<icon><![CDATA[".$usericon."]]></icon>";
					echo "<isdefaulticon>-1</isdefaulticon>";
					echo "<profileurl><![CDATA[".$SITEURI.$row['username']."]]></profileurl>";
				//好友组id、名称
				if($imxmlperf!='high')
				{
					echo "<buddygroup id=\"".$row['role_id']."\">".$row['name']."</buddygroup>";
				}
					echo "<sex>".$genders."</sex>";
					echo "<bday>".$bday."</bday>";
					echo "<province>".$row['province']."</province>";
					echo "<city>".$row['city']."</city>";
				if($isdetail)
				{
					$row["nickname"] = im_xmlsafestr(htmlspecialchars($row["nickname"]));
					echo "<nick>".$row['nickname']."</nick>";
					echo "<credit>".$row['credits']."</credit>";
					echo "<money>".$row['extcredits2']."</money>";
					echo "<email>".$row['email']."</email>";
				}
				echo "</buddy>";     
			}
		}  
	}
	if($isfuidlist)
	{
		echo $_fuid;
		echo '</buddyuids>';
	}
	else
	{
		echo '</buddylist>';
	}
	*/
}
//6-添加好友
function getAddBuddy($querysid, $queryuserid, $userkey, $newbuddyid) {
	   echo "<sessionvalide>true</sessionvalide>";
     echo "<addbuddyresult>true</addbuddyresult>";
}
//7-删除好友
function getDelBuddy ($querysid, $queryuserid, $userkey, $delbuddyid){
	   echo "<sessionvalide>true</sessionvalide>";
     echo "<delbuddyresult>"."true"."</delbuddyresult>";
}

//下面是辅助函数

//userkey验证用户登录
function sitekey_login($queryuserid, $userkey)
{
	global $sitekey,$dm_ntalker_version;
	echo '<version>'.$dm_ntalker_version.'</version>';
	if(!$queryuserid)
	{
		echo "<error>no uid param valid</error>";
		echo "<userkeyvalide>false</userkeyvalide>";
		return false;
	}
	if(!$userkey)
	{
		echo "<error>no userkey param valid</error>";
		echo "<userkeyvalide>false</userkeyvalide>";
		return false;
	}
	$tempkey = md5($queryuserid.$sitekey);
	if($tempkey == $userkey)
	{
		echo "<userkeyvalide>true</userkeyvalide>";
		return true;
	}
	else
	{
		echo "<userkeyvalide>false</userkeyvalide>";
		return false;
	}
}

//好友列表的页数
function page_start($page, $ppp, $totalnum)
{
	$totalpage = ceil($totalnum / $ppp);
	$page =  max(0, min($totalpage,intval($page)));
	return $page * $ppp;
}

//好友uid安全性检查
function uid_safecheck($fuids,$fnum)
{
	if(!$fuids)
	{
		return '';
	}
    $_fuid = explode(',',$fuids);
    if(count($_fuid)>$fnum)
    {
		return '';
	}
	$_fuids = '';
	$i = 0;
    foreach($_fuid as $_tmpuid)
    {
		//验证是否为数字uid
		if(is_numeric($_tmpuid))
		{
			if($i>=100)
			{
				break;
			}
			$_fuids =  $_fuids ? $_fuids.','.$_tmpuid : $_tmpuid;
			$i++;
		}
		else
		{
			break;
		}
	}
	if($_fuids)
	{
		return $_fuids;
	}
	else
	{
		return '';
	}
}
//处理特殊用户名       
function im_xmlsafestr($s)
{
	return preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$s);
}


?>
