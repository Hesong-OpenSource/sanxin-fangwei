<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_m_config`;");
E_C("CREATE TABLE `fanwe_m_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `val` text,
  `type` tinyint(1) NOT NULL,
  `sort` int(11) DEFAULT '0',
  `value_scope` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_m_config` values('10','kf_phone','客服电话','400-000-0000','0','1',NULL);");
E_D("replace into `fanwe_m_config` values('11','kf_email','客服邮箱','','0','2',NULL);");
E_D("replace into `fanwe_m_config` values('29','ios_upgrade','ios版本升级内容','','3','9',NULL);");
E_D("replace into `fanwe_m_config` values('16','page_size','分页大小','10','0','10',NULL);");
E_D("replace into `fanwe_m_config` values('17','about_info','关于我们(填文章ID)','66','0','3',NULL);");
E_D("replace into `fanwe_m_config` values('18','program_title','程序标题名称','众筹网','0','0',NULL);");
E_D("replace into `fanwe_m_config` values('22','android_version','android版本号(yyyymmddnn)','2014082101','0','4',NULL);");
E_D("replace into `fanwe_m_config` values('23','android_filename','android下载包名(放程序根目录下)','fanwe_P2P.apk','0','5',NULL);");
E_D("replace into `fanwe_m_config` values('24','ios_version','ios版本号(yyyymmddnn)','2014082009','0','7',NULL);");
E_D("replace into `fanwe_m_config` values('25','ios_down_url','ios下载地址(appstore连接地址)','','0','8',NULL);");
E_D("replace into `fanwe_m_config` values('28','android_upgrade','android版本升级内容','修复bug','3','6',NULL);");
E_D("replace into `fanwe_m_config` values('30','article_cate_id','文章分类ID','15','0','11',NULL);");
E_D("replace into `fanwe_m_config` values('31','android_forced_upgrade','android是否强制升级(0:否;1:是)','1','0','0',NULL);");
E_D("replace into `fanwe_m_config` values('32','ios_forced_upgrade','ios是否强制升级(0:否;1:是)','1','0','0',NULL);");
E_D("replace into `fanwe_m_config` values('35','logo','系统LOGO','./public/attachment/201508/17/12/55d162c8c491e.png','2','1',NULL);");
E_D("replace into `fanwe_m_config` values('33','index_adv_num','首页广告数','5','0','33',NULL);");
E_D("replace into `fanwe_m_config` values('34','index_pro_num','首页推荐商品数','5','0','34',NULL);");
E_D("replace into `fanwe_m_config` values('36','wx_appid','微信APPID','','0','36',NULL);");
E_D("replace into `fanwe_m_config` values('37','wx_secrit','微信SECRIT','','0','37',NULL);");
E_D("replace into `fanwe_m_config` values('38','sina_app_key','新浪APP_KEY','','0','38',NULL);");
E_D("replace into `fanwe_m_config` values('39','sina_app_secret','新浪APP_SECRET','','0','39',NULL);");
E_D("replace into `fanwe_m_config` values('40','sina_bind_url','新浪回调地址','','0','40',NULL);");
E_D("replace into `fanwe_m_config` values('41','qq_app_key','QQ登录APP_KEY','','0','41',NULL);");
E_D("replace into `fanwe_m_config` values('42','qq_app_secret','QQ登录APP_SECRET','','0','42',NULL);");
E_D("replace into `fanwe_m_config` values('43','wx_app_key','微信(分享)appkey','','0','43',NULL);");
E_D("replace into `fanwe_m_config` values('44','wx_app_secret','微信(分享)appSecret','','0','44',NULL);");
E_D("replace into `fanwe_m_config` values('45','wx_controll','一站式登录方式','0','4','45','0,1');");

require("../../inc/footer.php");
?>