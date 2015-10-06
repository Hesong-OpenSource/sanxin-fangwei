<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_deal_item`;");
E_C("CREATE TABLE `fanwe_deal_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `price` decimal(20,2) NOT NULL COMMENT '金额',
  `support_count` int(11) NOT NULL,
  `support_amount` decimal(20,2) NOT NULL COMMENT '支持量',
  `description` text NOT NULL,
  `is_delivery` tinyint(1) NOT NULL,
  `delivery_fee` decimal(20,2) NOT NULL COMMENT '支付金额',
  `is_limit_user` tinyint(1) NOT NULL COMMENT '是否限',
  `limit_user` int(11) NOT NULL COMMENT '限额数量',
  `repaid_day` int(11) NOT NULL COMMENT '项目成功后的回报时间',
  `virtual_person` int(11) NOT NULL,
  `is_share` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分红',
  `share_fee` decimal(20,2) NOT NULL COMMENT '分红金额',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 表示回报类型 1表示无私奉献',
  PRIMARY KEY (`id`),
  KEY `deal_id` (`deal_id`),
  KEY `price` (`price`)
) ENGINE=MyISAM AUTO_INCREMENT=249 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_deal_item` values('248','155','200.00','1','200.00','获得回报优惠券获得回报优惠券获得回报优惠券获得回报优惠券获得回报优惠券获得回报优惠券获得回报优惠券','0','0.00','0','0','0','0','0','0.00','0');");
E_D("replace into `fanwe_deal_item` values('247','155','100.00','0','0.00','获得回报优惠券获得回报优惠券获得回报优惠券','0','0.00','0','0','10','0','0','0.00','0');");

require("../../inc/footer.php");
?>