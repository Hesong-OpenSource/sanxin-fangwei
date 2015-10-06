<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_deal_item_image`;");
E_C("CREATE TABLE `fanwe_deal_item_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `deal_item_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `deal_id` (`deal_id`),
  KEY `deal_item_id` (`deal_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=189 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_deal_item_image` values('188','155','248','./public/attachment/201508/17/13/55d17065f2414.png');");
E_D("replace into `fanwe_deal_item_image` values('187','155','247','./public/attachment/201508/17/13/55d17052970d3.jpg');");

require("../../inc/footer.php");
?>