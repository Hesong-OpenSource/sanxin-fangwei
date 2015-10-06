<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_api_login`;");
E_C("CREATE TABLE `fanwe_api_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `config` text NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `bicon` varchar(255) NOT NULL,
  `is_weibo` tinyint(1) NOT NULL,
  `dispname` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_api_login` values('13','新浪api登录接口','a:3:{s:7:\"app_key\";s:1:\"1\";s:10:\"app_secret\";s:1:\"2\";s:7:\"app_url\";s:44:\"http://www.qmct8.com/api_callback.php?c=Sina\";}','Sina','./public/attachment/201210/13/17/50792e5bbc901.gif','./public/attachment/201210/13/16/5079277a72c9d.gif','1','新浪微博');");
E_D("replace into `fanwe_api_login` values('14','腾讯微博登录插件','a:3:{s:7:\"app_key\";s:1:\"1\";s:10:\"app_secret\";s:1:\"2\";s:7:\"app_url\";s:47:\"http://www.qmct8.com/api_callback.php?c=Tencent\";}','Tencent','./public/attachment/201211/06/11/509882825c183.png','./public/attachment/201211/06/11/50988287b1890.png','1','腾讯微博');");
E_D("replace into `fanwe_api_login` values('15','微信登录','N;','Weixin','/public/images/api_login/Weixin_api.png','/public/images/api_login/Weixin_api.png','0','微信登录');");

require("../../inc/footer.php");
?>