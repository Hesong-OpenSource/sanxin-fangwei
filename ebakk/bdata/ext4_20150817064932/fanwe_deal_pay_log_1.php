<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_deal_pay_log`;");
E_C("CREATE TABLE `fanwe_deal_pay_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `money` decimal(20,2) NOT NULL,
  `create_time` int(11) NOT NULL,
  `log_info` text NOT NULL,
  `comissions` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '佣金',
  `share_fee` decimal(20,2) NOT NULL,
  `delivery_fee` decimal(20,2) NOT NULL,
  `requestNo` varchar(255) NOT NULL COMMENT '是第三方支付的请求号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`requestNo`) USING BTREE,
  KEY `deal_id` (`deal_id`),
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='项目支持金额发放记录'");

require("../../inc/footer.php");
?>