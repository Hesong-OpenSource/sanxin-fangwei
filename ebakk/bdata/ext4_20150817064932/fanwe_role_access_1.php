<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `fanwe_role_access`;");
E_C("CREATE TABLE `fanwe_role_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8");
E_D("replace into `fanwe_role_access` values('87','5','0','133');");
E_D("replace into `fanwe_role_access` values('88','5','0','134');");
E_D("replace into `fanwe_role_access` values('89','5','0','92');");
E_D("replace into `fanwe_role_access` values('90','5','0','118');");
E_D("replace into `fanwe_role_access` values('91','5','0','117');");
E_D("replace into `fanwe_role_access` values('92','5','0','124');");
E_D("replace into `fanwe_role_access` values('93','5','0','132');");
E_D("replace into `fanwe_role_access` values('94','5','0','123');");
E_D("replace into `fanwe_role_access` values('95','5','0','127');");
E_D("replace into `fanwe_role_access` values('96','5','0','128');");

require("../../inc/footer.php");
?>