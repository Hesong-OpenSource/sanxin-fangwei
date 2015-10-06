<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_deal_visit_log`;");
E_C("CREATE TABLE `fanwe_deal_visit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `client_ip` varchar(255) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `deal_id` (`deal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24021 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_deal_visit_log` values('24020','155','123.14.81.83','1439762124');");
E_D("replace into `fanwe_deal_visit_log` values('24019','155','220.181.132.15','1439761843');");
E_D("replace into `fanwe_deal_visit_log` values('24018','155','101.199.112.54','1439761214');");
E_D("replace into `fanwe_deal_visit_log` values('24017','155','220.181.132.215','1439761119');");
E_D("replace into `fanwe_deal_visit_log` values('24016','156','123.14.81.83','1439760892');");
E_D("replace into `fanwe_deal_visit_log` values('24015','155','123.14.81.83','1439760399');");

require("../../inc/footer.php");
?>