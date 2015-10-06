<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_money_type`;");
E_C("CREATE TABLE `fanwe_money_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名称',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT 'type类型 0 ~ ？',
  `class` varchar(100) NOT NULL DEFAULT '' COMMENT '所属分类 money  lock_money site_money  point  score',
  `sort` int(11) NOT NULL DEFAULT '0',
  `is_effect` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_money_type` values('1','普通的','0','money','0','1');");
E_D("replace into `fanwe_money_type` values('2','加入诚意金','1','money','0','1');");
E_D("replace into `fanwe_money_type` values('3','违约扣除诚意金','2','money','0','1');");
E_D("replace into `fanwe_money_type` values('4','分红','3','money','0','1');");
E_D("replace into `fanwe_money_type` values('5','订金','4','money','0','1');");
E_D("replace into `fanwe_money_type` values('6','首付','5','money','0','1');");
E_D("replace into `fanwe_money_type` values('7','众筹买房','6','money','0','1');");
E_D("replace into `fanwe_money_type` values('8','买房卖出回报','7','money','0','1');");
E_D("replace into `fanwe_money_type` values('9','理财赎回本金','8','money','0','1');");
E_D("replace into `fanwe_money_type` values('10','理财赎回收益','9','money','0','1');");
E_D("replace into `fanwe_money_type` values('11','理财赎回手续费','10','money','0','1');");
E_D("replace into `fanwe_money_type` values('12','理财本金','11','money','0','1');");
E_D("replace into `fanwe_money_type` values('13','理财购买手续费','12','money','0','1');");
E_D("replace into `fanwe_money_type` values('14','理财冻结资金','13','money','0','1');");
E_D("replace into `fanwe_money_type` values('15','理财服务费','14','money','0','1');");
E_D("replace into `fanwe_money_type` values('16','理财发放资金','15','money','0','1');");

require("../../inc/footer.php");
?>