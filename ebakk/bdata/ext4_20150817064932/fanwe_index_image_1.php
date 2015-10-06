<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_index_image`;");
E_C("CREATE TABLE `fanwe_index_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 表示首页轮播 1表示产品页轮播 2表示股权轮播',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_index_image` values('30','./public/attachment/201508/17/12/55d16265716d2.jpg','','3','3','0');");
E_D("replace into `fanwe_index_image` values('29','./public/attachment/201508/17/12/55d1625bba1be.JPG','','2','2','0');");
E_D("replace into `fanwe_index_image` values('28','./public/attachment/201508/17/12/55d1624c80f85.jpg','','1','1','0');");

require("../../inc/footer.php");
?>