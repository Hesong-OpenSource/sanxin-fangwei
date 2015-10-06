<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_licai_fund_type`;");
E_C("CREATE TABLE `fanwe_licai_fund_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '产品名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态1:有效;0无效',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='基金种类：\r\n全部 货币型 股票型 债券型 混合型 理财型 指数型 QDII 其他型'");
E_D("replace into `fanwe_licai_fund_type` values('1','货币型','1','0');");
E_D("replace into `fanwe_licai_fund_type` values('2','股票型','1','0');");
E_D("replace into `fanwe_licai_fund_type` values('3','债券型','1','0');");
E_D("replace into `fanwe_licai_fund_type` values('4','混合型','1','0');");
E_D("replace into `fanwe_licai_fund_type` values('5','理财型','1','0');");
E_D("replace into `fanwe_licai_fund_type` values('6','标准','1','0');");
E_D("replace into `fanwe_licai_fund_type` values('7','QDII','1','0');");
E_D("replace into `fanwe_licai_fund_type` values('8','其他型','1','0');");
E_D("replace into `fanwe_licai_fund_type` values('9','中欧','1','0');");

require("../../inc/footer.php");
?>