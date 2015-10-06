<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_role_nav`;");
E_C("CREATE TABLE `fanwe_role_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_role_nav` values('1','首页','0','1','1');");
E_D("replace into `fanwe_role_nav` values('3','系统设置','0','1','10');");
E_D("replace into `fanwe_role_nav` values('5','会员管理','0','1','3');");
E_D("replace into `fanwe_role_nav` values('10','短信邮件','0','1','7');");
E_D("replace into `fanwe_role_nav` values('13','项目管理','0','1','4');");
E_D("replace into `fanwe_role_nav` values('14','支付管理','0','1','5');");
E_D("replace into `fanwe_role_nav` values('11','前端设置','0','1','6');");
E_D("replace into `fanwe_role_nav` values('15','统计模块','0','1','8');");

require("../../inc/footer.php");
?>