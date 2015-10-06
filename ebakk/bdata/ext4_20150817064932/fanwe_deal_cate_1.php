<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_deal_cate`;");
E_C("CREATE TABLE `fanwe_deal_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_deal_cate` values('1','餐饮','2','0','0');");
E_D("replace into `fanwe_deal_cate` values('2','服务','1','0','0');");
E_D("replace into `fanwe_deal_cate` values('3','教育','4','0','0');");
E_D("replace into `fanwe_deal_cate` values('4','商品','8','0','0');");
E_D("replace into `fanwe_deal_cate` values('5','农业','9','0','0');");
E_D("replace into `fanwe_deal_cate` values('6','科技','6','0','0');");
E_D("replace into `fanwe_deal_cate` values('7','娱乐','5','0','0');");
E_D("replace into `fanwe_deal_cate` values('8','艺术','7','0','0');");
E_D("replace into `fanwe_deal_cate` values('9','健身','3','0','0');");
E_D("replace into `fanwe_deal_cate` values('10','其他','10','0','0');");

require("../../inc/footer.php");
?>