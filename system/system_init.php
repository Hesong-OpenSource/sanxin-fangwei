<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------
 if(!defined('APP_ROOT_PATH')) 
	define('APP_ROOT_PATH', str_replace('system/system_init.php', '', str_replace('\\', '/', __FILE__)));
require APP_ROOT_PATH."license";
//关于安装的检测


if(!file_exists(APP_ROOT_PATH."public/install.lock"))
{
	app_redirect(APP_ROOT."/install/index.php");
}	
if(IS_DEBUG){
	ini_set("display_errors", 1);
 	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
 	$GLOBALS['msg']->set_debug(true);
}
else
	error_reporting(0);
?>