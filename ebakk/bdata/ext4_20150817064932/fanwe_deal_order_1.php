<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_deal_order`;");
E_C("CREATE TABLE `fanwe_deal_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `deal_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `pay_time` int(11) NOT NULL,
  `total_price` decimal(20,2) NOT NULL COMMENT '总价',
  `delivery_fee` decimal(20,2) NOT NULL COMMENT '运费',
  `deal_price` decimal(20,2) NOT NULL COMMENT '项目费用',
  `support_memo` text NOT NULL,
  `payment_id` int(11) NOT NULL,
  `bank_id` varchar(255) NOT NULL,
  `credit_pay` decimal(20,2) NOT NULL COMMENT '信贷付款',
  `online_pay` decimal(20,2) NOT NULL COMMENT '在线付款',
  `deal_name` varchar(255) NOT NULL,
  `order_status` tinyint(1) NOT NULL COMMENT '0:未支付 1:已支付(过期) 2:已支付(无库存) 3:成功',
  `create_time` int(11) NOT NULL,
  `consignee` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `is_success` tinyint(1) NOT NULL,
  `repay_time` int(11) NOT NULL COMMENT '回报更新时间',
  `repay_memo` text NOT NULL COMMENT '回报备注，由发起人更新',
  `is_refund` tinyint(1) NOT NULL COMMENT '已退款 0:未 1:已',
  `is_has_send_success` tinyint(1) NOT NULL,
  `repay_make_time` int(11) NOT NULL DEFAULT '0' COMMENT '回报确认时间',
  `share_fee` decimal(20,2) NOT NULL COMMENT '分红金额',
  `share_status` tinyint(1) NOT NULL COMMENT '分红是否发放：0未发放，1已发',
  `is_tg` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是第三方托管',
  `score` int(11) NOT NULL COMMENT '付款积分',
  `score_money` decimal(20,2) NOT NULL COMMENT '积分对换的余额,对换的余额已加到余额支付里，这里记录是用在查看，退款时用',
  `sp_multiple` varchar(255) NOT NULL COMMENT '记录\"购买送支付金额的几倍信用/积分\"的倍数的反序列数组array(\"score_multiple\"=>''倍数'',\"point_multiple\"=>''倍数''）,退款时用',
  `logistics_company` varchar(255) NOT NULL COMMENT '物流公司',
  `logistics_links` varchar(255) NOT NULL COMMENT '物流链接',
  `logistics_number` varchar(255) NOT NULL COMMENT '物流单号',
  `invest_id` int(11) NOT NULL COMMENT '投资id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单类型 0表示普通众筹 1表示股权众筹 2表示无私奉献',
  `requestNo` varchar(255) NOT NULL COMMENT 'yeepay_log.id',
  `is_complete_transaction` tinyint(1) NOT NULL COMMENT '0 表示未放款 1表示放款  2表示退款',
  `fee` decimal(20,2) NOT NULL COMMENT '手续费',
  `targetAmount` decimal(20,2) NOT NULL COMMENT '获取的目标金额',
  `progress` tinyint(1) NOT NULL DEFAULT '0' COMMENT '项目进度 0 表示未支付 2表示已支付定金 3表示支付首付 4表示退款处理 5放款处理',
  PRIMARY KEY (`id`),
  KEY `deal_id` (`deal_id`),
  KEY `deal_item_id` (`deal_item_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2147 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_deal_order` values('2146','156','0','21','myname1','0','10000000.00','0.00','10000000.00','','0','','1599999.99','0.00','度假酒店','0','1439761559','','','','','','','1','0','','0','0','1439761993','0.00','0','0','0','0.00','','','','','2','1','','0','0.00','0.00','0');");
E_D("replace into `fanwe_deal_order` values('2145','155','248','20','myname','1439760547','200.00','0.00','200.00','','0','','200.00','0.00','项目名称：泡温泉','3','1439760547','','','','','','','0','0','','0','0','1439760554','0.00','0','0','0','0.00','a:2:{s:14:\"score_multiple\";d:0.5;s:14:\"point_multiple\";d:0.5;}','','','','0','0','','0','0.00','0.00','0');");

require("../../inc/footer.php");
?>