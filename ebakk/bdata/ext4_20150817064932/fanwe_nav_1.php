<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_nav`;");
E_C("CREATE TABLE `fanwe_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `blank` tinyint(1) NOT NULL,
  `sort` int(11) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `u_module` varchar(255) NOT NULL,
  `u_action` varchar(255) NOT NULL,
  `u_id` int(11) NOT NULL,
  `u_param` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_nav` values('42','首页','','0','1','1','index','','0','');");
E_D("replace into `fanwe_nav` values('47','回报众筹','deals.html','0','3','1','','','0','');");
E_D("replace into `fanwe_nav` values('46','股权众筹','deals/type-1.html','0','2','1','','','0','');");
E_D("replace into `fanwe_nav` values('48','最新动态','','0','4','1','news','index','0','');");
E_D("replace into `fanwe_nav` values('49','投资人','','0','5','1','investor','invester_list','0','');");
E_D("replace into `fanwe_nav` values('51','路演快讯','','0','6','1','article_cate','','0','');");

require("../../inc/footer.php");
?>